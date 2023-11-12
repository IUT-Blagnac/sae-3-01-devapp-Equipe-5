import json
import yaml
import paho.mqtt.client as mqtt
import time

# Lecture du fichier de configuration
with open("config.yaml", "r") as file:
    print("~ Retrieving configuration file")
    config = yaml.safe_load(file)


def on_connect(client, userdata, flags, rc):
    print("~ Connected with result code " + str(rc))
    client.subscribe(config["topic"])
    print("~ Subscribed to " + config["topic"])
    print("~ Saving data on : " + config["dataFile"])
    print("~ Waiting for data \n")


def on_disconnect(client, userdata, rc):
    if rc != 0:
        print("Unexpected disconnection.")

# Affichage de la moyenne des données de la pièce reçue 
def affichage_Moyenne(room):
    with open(config["dataFile"], "r+") as outfile:
        data = json.load(outfile)
        if room in data:
            (
                temp,
                humidity,
                co2,
                activity,
                tvoc,
                illumination,
                infrared,
                infrared_and_visible,
                pressure,
            ) = ([], [], [], [], [], [], [], [], [])

            for i in data[room]:
                temp.append(float(i["temperature"]))
                humidity.append(float(i["humidite"]))
                co2.append(float(i["Co2"]))
                activity.append(float(i["activity"]))
                tvoc.append(float(i["tvoc"]))
                illumination.append(float(i["illumination"]))
                infrared.append(float(i["infrared"]))
                infrared_and_visible.append(float(i["infrared_and_visible"]))
                pressure.append(float(i["pressure"]))
            print(
                "["
                + room
                + "][AVERAGE] Temp "
                + str(round(sum(temp) / len(temp), 2))
                + "ºC Hum "
                + str(round(sum(humidity) / len(humidity), 2))
                + "% Co2 "
                + str(round(sum(co2) / len(co2), 2))
                + "ppm Act "
                + str(round(sum(activity) / len(activity), 2))
                + " Tvoc "
                + str(round(sum(tvoc) / len(tvoc), 2))
                + "ppb Illum "
                + str(round(sum(illumination) / len(illumination), 2))
                + "lux Infra "
                + str(round(sum(infrared) / len(infrared), 2))
                + "lux InfraVis "
                + str(round(sum(infrared_and_visible) / len(infrared_and_visible), 2))
                + "lux Press "
                + str(round(sum(pressure) / len(pressure), 2))
                + "hPa"
            )

# Ecriture des données dans le fichier de données reçu en paramètre
def write_data(
    room,
    temp,
    humidity,
    co2,
    activity,
    tvoc,
    illumination,
    infrared,
    infrared_and_visible,
    pressure,
):
    try:
        jsonFile = open(config["dataFile"], "r")
    except IOError:
        jsonFile = open(config["dataFile"], "w")
        jsonFile.write("{}")
        print("~ Creating file " + config["dataFile"])
        jsonFile.close()
        jsonFile = open(config["dataFile"], "r")

    data = json.load(jsonFile)

    if room in data:
        if len(data[room]) >= 10:
            data[room].pop(0)
        data[room].append(
            {
                "temperature": temp,
                "humidite": humidity,
                "Co2": co2,
                "activity": activity,
                "tvoc": tvoc,
                "illumination": illumination,
                "infrared": infrared,
                "infrared_and_visible": infrared_and_visible,
                "pressure": pressure,
                "time": time.time(),
            }
        )
    else:
        data[room] = [
            {
                "temperature": temp,
                "humidite": humidity,
                "Co2": co2,
                "activity": activity,
                "tvoc": tvoc,
                "illumination": illumination,
                "infrared": infrared,
                "infrared_and_visible": infrared_and_visible,
                "pressure": pressure,
                "time": time.time(),
            }
        ]

    data = json.dumps(data, indent=4)
    with open(config["dataFile"], "w") as outfile:
        outfile.write(data)
        outfile.close()


def on_message(client, userdata, msg):
    # reception des données
    my_data = msg.payload.decode("utf-8")
    my_json = json.loads(my_data)

    keys = [
        "temperature",
        "humidity",
        "co2",
        "activity",
        "tvoc",
        "illumination",
        "infrared",
        "infrared_and_visible",
        "pressure",
    ]
    try:
        if all(key in my_json[0] for key in keys):
            pass
    except Exception:
        return

    room = my_json[1]["room"]
    temp = my_json[0]["temperature"]
    humidity = my_json[0]["humidity"]
    co2 = my_json[0]["co2"]
    activity = my_json[0]["activity"]
    tvoc = my_json[0]["tvoc"]
    illumination = my_json[0]["illumination"]
    infrared = my_json[0]["infrared"]
    infrared_and_visible = my_json[0]["infrared_and_visible"]
    pressure = my_json[0]["pressure"]

    write_data(
        room,
        temp,
        humidity,
        co2,
        activity,
        tvoc,
        illumination,
        infrared,
        infrared_and_visible,
        pressure,
    )
    
    # print de toutes les données reçues en une seule ligne
    print(
        "["
        + room
        + "] "
        + str(temp)
        + "ºC "
        + str(humidity)
        + "% "
        + str(co2)
        + "ppm "
        + str(activity)
        + " "
        + str(tvoc)
        + "ppb "
        + str(illumination)
        + "lux "
        + str(infrared)
        + "lux "
        + str(infrared_and_visible)
        + "lux "
        + str(pressure)
        + "hPa"
    )

    # Affichage de la moyenne
    affichage_Moyenne(room)
    print("")


def on_publish(client, userdata, mid):
    print("mid: " + str(mid))


client = mqtt.Client()
client.on_connect = on_connect
client.on_message = on_message

client.connect(config["url"], config["port"], config["keepalive"])
client.loop_forever()
