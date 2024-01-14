import csv
import yaml
import paho.mqtt.client as mqtt
import time
import json
import signal

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
            # Pour chaque donnée sélectionnée dans le fichier de configuration, on calcule la moyenne
            for key in config["selectedData"]:
                if key != "time":
                    values = [
                        float(row[key])
                        for row in data
                        if key in row and row[key].strip()
                    ]
                    if values:
                        moyenne = round(sum(values) / len(values), 2)
                        message += key + " : " + str(moyenne) + " "

            print(message + "\n")

# Fonction appelée à chaque réception de message MQTT
def on_message(client, userdata, msg):
    my_data = msg.payload.decode("utf-8")
    my_json = json.loads(my_data)

    # check si le nom de la salle est présent et si non, on passe à la suite
    try:
        room = my_json[1]["room"]
    except Exception as e:
        print("~ Erreur lors de la réception des données ")
        return

    # check si il y a des données dans le json
    if len(my_json[0]) == 0:
        return

    # enregistrement des données dans un dictionnaire avec le temps 
    # (si les données sont présentes dans le fichier de configuration et dans le json)
    data_values = {
        key: my_json[0][key] for key in config["selectedData"] if key in my_json[0]
    }
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
        csv_writer.writerow(
            [room, data_values["time"]]
            + [data_values[key] for key in config["selectedData"]]
        )

    text = ""
    # Check si les données dépassent les seuils et écriture dans le fichier d'alerte le cas échéant
    with open(config["alertFile"], "a", newline="") as alertfile:
        alert_writer = csv.writer(alertfile)

        # Pour chaque seuil défini dans le fichier de configuration, on vérifie si la donnée dépasse le seuil
        for key, threshold in config["thresholds"].items():

            if key in data_values and data_values[key] > threshold:
                print(
                    "Threshold exceeded - {}: {} (Threshold: {})".format(
                        key, data_values[key], threshold
                    )
                )
                text += (
                    str(key)
                    + " "
                    + str(data_values[key])
                    + " ("
                    + str(threshold)
                    + ") "
                )
        alert_writer.writerow([room, data_values["time"], text])

    affichage_Moyenne(room)
    print("")

# Connexion au broker MQTT

client = mqtt.Client()
client.on_connect = on_connect
client.on_message = on_message

client.connect(config["url"], config["port"], config["keepalive"])


# Définition des temps d'exécution et de repos
running_time = config["running_time"]*60  # temps d'éxécution transformé de minutes en secondes
rest_duration = config["rest_duration"]*60  # temps de repos transformé de minutes en secondes
if rest_duration == 0:
    rest_duration = 1
# Fonction appelée à chaque déclenchement d'alarme

def handle_execution(signum, frame):
    print("Exécution pendant {} secondes...".format(running_time))
    start_time = time.time()
    end_time = start_time + running_time

    while time.time() < end_time:
        client.loop(timeout=1.0, max_packets=1)

    # Repos après la période d'exécution
    signal.alarm(rest_duration)  # Définition de l'alarme pour la période de repos
    print("Pause pendant {} secondes...".format(rest_duration))


# Définition des signaux d'alarme pour les périodes d'exécution et de repos
signal.signal(signal.SIGALRM, handle_execution)

# Activation de l'alarme pour la première période d'exécution 
# (2 secondes le temps que la connection se fasse)
signal.alarm(2)

# Boucle infinie pour attendre les signaux d'alarme
while True:
    signal.pause()  # Pause jusqu'à ce qu'un signal d'alarme se déclenche
