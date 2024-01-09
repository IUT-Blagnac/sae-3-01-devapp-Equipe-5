from behave import given, when, then
import paho.mqtt.client as mqtt
import yaml

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
