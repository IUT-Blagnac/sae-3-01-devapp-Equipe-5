import csv
import yaml
import paho.mqtt.client as mqtt
import time
import json

# Lecture du fichier de configuration
with open("configuration.yaml", "r") as file:
    print("~ Retrieving configuration file")
    config = yaml.safe_load(file)

print("~ Selected data : " + str(config["selectedData"]))

def on_connect(client, userdata, flags, rc):
    print("~ Connected with result code " + str(rc))
    
    # Abonnement aux différents topics MQTT définis dans le fichier de configuration
    for topic in config["topics"]:
        try:
            client.subscribe(topic)
            print("~ Subscribed to " + topic)
        except Exception as e:
            print("~ Failed to subscribe to {}: {}".format(topic, str(e)))

    # Creation du fichier de données si il n'existe pas
    try:
        csvFile = open(config["dataFile"], "r", newline="")
    except IOError:
        csvFile = open(config["dataFile"], "w", newline="")
        csv_writer = csv.writer(csvFile)
        csv_writer.writerow(["room", "time"] + config["selectedData"])
        print("~ Creating file " + config["dataFile"])
        csvFile.close()

    # Creation du fichier d'alerte si il n'existe pas
    try:
        alertFile = open(config["alertFile"], "r", newline="")
    except IOError:
        alertFile = open(config["alertFile"], "w", newline="")
        alert_writer = csv.writer(alertFile)
        alert_writer.writerow(["room", "time", "alert"])
        print("~ Creating file " + config["alertFile"])
        alertFile.close()

    print("~ Saving data on : " + config["dataFile"])
    print("~ Waiting for data \n")


# Affichage de la moyenne des données de la pièce
def affichage_Moyenne(room):
    with open(config["dataFile"], "r") as csvfile:
        csv_reader = csv.DictReader(csvfile)
        data = [row for row in csv_reader if row["room"] == room]
        if data:
            print("Moyenne des données de la pièce " + room + " :")
            message = ""
            for key in config["selectedData"]:
                if key != "time":
                    values = [float(row[key]) for row in data if key in row and row[key].strip()]
                    if values:
                        moyenne = round(sum(values) / len(values), 2)
                        message += key + " : " + str(moyenne) + " "

            print(message + "\n")


def on_message(client, userdata, msg):
    my_data = msg.payload.decode("utf-8")
    my_json = json.loads(my_data)
    
    #check si le nom de la salle est présent et si non, on passe à la suite
    try:
        room = my_json[1]["room"]
    except Exception as e:
        print("~ Nom de salle absent")
        return
    
    # check si il y a des données dans le json
    if len(my_json[0]) == 0:
        return
    
    data_values = {key: my_json[0][key] for key in config["selectedData"] if key in my_json[0]}
    data_values["time"] = time.time()

    # Affichage des données reçues
    print(
        "["
        + room
        + "] "
        + str(data_values).replace("{", "").replace("}", "").replace("'", "")
    )

    # Écriture des données dans le fichier CSV
    with open(config["dataFile"], "a", newline="") as csvfile:
        csv_writer = csv.writer(csvfile)
        csv_writer.writerow([room, data_values["time"]] + [data_values[key] for key in config["selectedData"]])

    text = ""
    # Check si les données dépassent les seuils et écriture dans le fichier d'alerte le cas échéant
    with open(config["alertFile"], "a", newline="") as alertfile:
        alert_writer = csv.writer(alertfile)
        for key, threshold in config["thresholds"].items():
            if key in data_values and data_values[key] > threshold:
                print("Threshold exceeded - {}: {} (Threshold: {})".format(key, data_values[key], threshold))
                text += str(key) + " "+ str(data_values[key]) + " (" + str(threshold) + ") "
        alert_writer.writerow([room, data_values["time"], text])

    affichage_Moyenne(room)
    print("")



client = mqtt.Client()
client.on_connect = on_connect
client.on_message = on_message

client.connect(config["url"], config["port"], config["keepalive"])
client.loop_forever()
