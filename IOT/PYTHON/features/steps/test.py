from behave import given, when, then
import paho.mqtt.client as mqtt
import yaml
import csv
import json
import threading
import time
import sys

def on_connect(client, userdata, flags, rc):
    client.connected_flag = (rc == 0)  # rc 0 means successful connection

def on_subscribe(client, userdata, mid, granted_qos):
    if all(qos >= 0 for qos in granted_qos):
        client.subscribed_flag = True
    else:
        client.subscribed_flag = False


@given('an available MQTT server')
def step_impl(context):
    context.server_url = "chirpstack.iut-blagnac.fr"  
    context.server_port = 1883
    context.client = mqtt.Client()
    context.client.connected_flag = False 

@given('a connected MQTT client')
def step_impl(context):
    context.client = mqtt.Client()
    context.client.on_connect = on_connect
    context.client.connected_flag = False
    context.client.subscribed_flag = False
    context.client.connect("chirpstack.iut-blagnac.fr", 1883, 60)
    context.client.loop_start()
    while not context.client.connected_flag:
        pass  # Wait for connection


@given('a connected MQTT client subscribed to topics')
def step_impl(context):
    with open("configuration.yaml", "r") as file:
        config = yaml.safe_load(file)
    context.client = mqtt.Client()
    context.client.on_connect = on_connect
    context.client.connect(config["url"], config["port"], config["keepalive"])
    context.client.loop_start()
    while not hasattr(context.client, 'connected_flag') or not context.client.connected_flag:
        time.sleep(0.1)  # Wait for connection
    for topic in config["topics"]:
        context.client.subscribe(topic)
    time.sleep(1)  # Wait for subscription


@when('the client attempts to connect')
def step_impl(context):
    context.client.on_connect = on_connect
    try:
        context.client.connect(context.server_url, context.server_port, 60)
        context.client.loop_start()  
    except Exception as e:
        context.connection_exception = e

@then('a connection is successfully established')
def step_impl(context):
    context.client.loop_stop()
    if hasattr(context, 'connection_exception'):
        raise context.connection_exception
    assert context.client.connected_flag == True

@when('the client subscribes to a topic')
def step_impl(context):

    with open("configuration.yaml", "r") as file:
        config = yaml.safe_load(file)

    context.client.on_subscribe = on_subscribe
    for topic in config["topics"]:
        try:
            context.client.subscribe(topic)
            print("~ Subscribed to " + topic)
            context.client.subscribed_flag = True
        except Exception as e:
            print("~ Failed to subscribe to {}: {}".format(topic, str(e)))
            context.client.subscribed_flag = False

@then('the subscription is successful')
def step_impl(context):
    assert context.client.subscribed_flag == True
    context.client.loop_stop()


def publish_test_message(client, topic, message):
    client.publish(topic, json.dumps(message))

@when('a message is published to a subscribed topic')
def step_impl(context):
    with open("configuration.yaml", "r") as file:
        config = yaml.safe_load(file)

    test_topic = "AM107/by-room/E208/data"
    test_message_data = {
        "temperature": 21, 
        "humidity": 59,
        "co2": 1371,
        "activity": 0,
        "tvoc": 391,
        "illumination": 2,
        "infrared": 2,
        "infrared_and_visible": 5,
        "pressure": 993.3
    }
    test_message_info = {
        "deviceName": "AM107-TestDevice",
        "devEUI": "00a1b2c3d4e5f678",
        "room": "B106",
        "floor": 2,
        "Building": "E"
    }
    test_message = [test_message_data, test_message_info]
    thread = threading.Thread(target=publish_test_message, args=(context.client, test_topic, test_message))
    thread.start()
    thread.join()
    time.sleep(1)  # Permettre le traitement du message


@then('the message is received and processed correctly')
def step_impl(context):
    with open("configuration.yaml", "r") as file:
        config = yaml.safe_load(file)
    expected_data = {
        "temperature": 21, 
        "humidity": 59,
        "co2": 1371,
        "activity": 0,
        "tvoc": 391,
        "illumination": 2,
        "infrared": 2,
        "infrared_and_visible": 5,
        "pressure": 993.3
    }
    with open(config["dataFile"], mode='r') as csvfile:
        csv_reader = csv.DictReader(csvfile)
        for row in csv_reader:
            if all(float(row[key]) == value for key, value in expected_data.items()):
                break
        else:
            assert False, "Les données attendues ne sont pas présentes dans le CSV"

