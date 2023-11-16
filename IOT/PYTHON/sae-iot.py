import json
import yaml
import paho.mqtt.client as mqtt
import time

# Lecture du fichier de configuration
with open("config.yaml", "r") as file:
    print("~ Retrieving configuration file")
    config = yaml.safe_load(file)

# Données a enregistrer
#config["selectedData"] config["selectedData"]: ["temperature"," humidity"," co2"," activity"," tvoc ","illumination"," infrared ","infrared_and_visible ","pressure "]


print("~ Selected data : " + str(config["selectedData"]))


def on_connect(client, userdata, flags, rc):
    print("~ Connected with result code " + str(rc))
    client.subscribe(config["topic"])
    print("~ Subscribed to " + config["topic"])

    # Création du fichier de données si il n'existe pas
    try:
        jsonFile = open(config["dataFile"], "r")
    except IOError:
        jsonFile = open(config["dataFile"], "w")
        jsonFile.write("{}")
        print("~ Creating file " + config["dataFile"])
        jsonFile.close()

    print("~ Saving data on : " + config["dataFile"])
    print("~ Waiting for data \n")


# Affichage de la moyenne des données de la pièce reçue
def affichage_Moyenne(room):
    with open(config["dataFile"], "r+") as outfile:
        data = json.load(outfile)
        if room in data:
            print("Moyenne des données de la pièce " + room + " :")
            message = ""
            for key in data[room][0]:
                if key != "time":
                    moyenne = 0
                    for i in range(len(data[room])):
                        moyenne += data[room][i][key]
                    moyenne = round(moyenne / len(data[room]), 2)
                    message += key + " : " + str(moyenne) + " "
            print(message + "\n")


def on_message(client, userdata, msg):
    my_data = msg.payload.decode("utf-8")
    my_json = json.loads(my_data)
    jsonFile = open(config["dataFile"], "r")
    
    # Variable qui contient la pièce (future clé du dictionnaire)   
    room = my_json[1][
        "room"
    ]  
    # Création d'un dictionnaire avec les données reçues qui nous intéressent et ajout de la date et l'heure
    data_values = {key: my_json[0][key] for key in config["selectedData"] if key in my_json[0]}
    data_values["time"] = time.time()

    # Affichage des données reçues
    print(
        "["
        + room
        + "] "
        + str(data_values).replace("{", "").replace("}", "").replace("'", "")
    )


    # Ecriture des données dans le fichier json
    existing_data = json.load(jsonFile)
    if room in existing_data:
        if len(existing_data[room]) >= 10:
            existing_data[room].pop(0)
        existing_data[room].append(data_values)
    else:
        existing_data[room] = [data_values]
    existing_data = json.dumps(existing_data, indent=4)
    with open(config["dataFile"], "w") as outfile:
        outfile.write(existing_data)
        outfile.close()

    affichage_Moyenne(room)
    print("")


client = mqtt.Client()
client.on_connect = on_connect
client.on_message = on_message

client.connect(config["url"], config["port"], config["keepalive"])
client.loop_forever()
