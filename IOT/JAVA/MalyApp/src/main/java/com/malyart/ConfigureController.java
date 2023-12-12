package com.malyart;

import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.nio.file.Files;
import java.io.BufferedReader;
import java.io.BufferedWriter;
import java.io.File;
import java.io.FileInputStream;
import java.io.FileWriter;
import javafx.fxml.FXML;
import javafx.scene.control.Alert;
import javafx.scene.control.Alert.AlertType;
import javafx.scene.control.Button;
import javafx.scene.control.ComboBox;
import javafx.scene.control.TextField;
import javafx.stage.Stage;

public class ConfigureController {

    @FXML
    private Button buttonQuitter;
    @FXML
    private Button buttonConfirmer;
    @FXML
    private TextField topicsField;
    @FXML
    private TextField urlField;
    @FXML
    private TextField portField;
    @FXML
    private TextField dataFileField;
    @FXML
    private TextField alertFileField;
    @FXML
    private ComboBox<String> restDurationComboBox;
    @FXML
    private TextField temperatureTextField;
    @FXML
    private TextField humidityTextField;
    @FXML
    private TextField co2TextField;
    @FXML
    private TextField activityTextField;
    @FXML
    private TextField tvocTextField;
    @FXML
    private TextField illuminationTextField;
    @FXML
    private TextField infraredTextField;
    @FXML
    private TextField infrared_and_visibleTextField;
    @FXML
    private TextField pressureTextField;

    File fileOldConfigFile = new File("./configuration.csv");
    File fileNewConfigFile = new File("./configuration.yaml");

    @FXML
    public void initialize() {

        restDurationComboBox.getItems().addAll("10", "20", "30", "60");

        File configExists = new File("./configuration.csv");

        String ligne;

        if (configExists.exists()) {

            try {

                InputStream ips = new FileInputStream(configExists);
                InputStreamReader ipsr = new InputStreamReader(ips);
                BufferedReader br = new BufferedReader(ipsr);

                while ((ligne = br.readLine()) != null) {
                    String valeur = "";

                    // Suite de conditions pour remplir les champs automatiquement

                    if (ligne.startsWith("url: ")) {
                        valeur = ligne.replace("url: ", "");
                        valeur = valeur.replace("\"", "");
                        urlField.setText(valeur);
                    } else if (ligne.startsWith("port: ")) {
                        valeur = ligne.replace("port: ", "");
                        portField.setText(valeur);
                    } else if (ligne.startsWith("dataFile: ")) {
                        valeur = ligne.replace("dataFile: ", "");
                        valeur = valeur.replace("\"", "");
                        dataFileField.setText(valeur);
                    } else if (ligne.startsWith("alertFile: ")) {
                        valeur = ligne.replace("alertFile: ", "");
                        valeur = valeur.replace("\"", "");
                        alertFileField.setText(valeur);
                    } else if (ligne.startsWith("topics: ")) {
                        valeur = ligne.replace("topics: ", "");
                        valeur = valeur.replace("[\"", "");
                        valeur = valeur.replace("\",\"", ",");
                        valeur = valeur.replace("\"]", "");
                        topicsField.setText(valeur);
                    } else if (ligne.startsWith("rest_duration  : ")) {
                        valeur = ligne.replace("rest_duration  : ", "");
                        restDurationComboBox.getSelectionModel().select(2);
                    } else if (ligne.startsWith("  temperature : ")) {
                        valeur = ligne.replace("  temperature : ", "");
                        temperatureTextField.setText(valeur);
                    } else if (ligne.startsWith("  humidity : ")) {
                        valeur = ligne.replace("  humidity : ", "");
                        humidityTextField.setText(valeur);
                    } else if (ligne.startsWith("  co2 : ")) {
                        valeur = ligne.replace("  co2 : ", "");
                        co2TextField.setText(valeur);
                    } else if (ligne.startsWith("  activity : ")) {
                        valeur = ligne.replace("  activity : ", "");
                        activityTextField.setText(valeur);
                    } else if (ligne.startsWith("  tvoc : ")) {
                        valeur = ligne.replace("  tvoc : ", "");
                        tvocTextField.setText(valeur);
                    } else if (ligne.startsWith("  illumination : ")) {
                        valeur = ligne.replace("  illumination : ", "");
                        illuminationTextField.setText(valeur);
                    } else if (ligne.startsWith("  infrared : ")) {
                        valeur = ligne.replace("  infrared : ", "");
                        infraredTextField.setText(valeur);
                    } else if (ligne.startsWith("  infrared_and_visible : ")) {
                        valeur = ligne.replace("  infrared_and_visible : ", "");
                        infrared_and_visibleTextField.setText(valeur);
                    } else if (ligne.startsWith("  pressure : ")) {
                        valeur = ligne.replace("  pressure : ", "");
                        pressureTextField.setText(valeur);
                    }
                }

                br.close();
            } catch (Exception e) {
                System.out.println(e.toString());
            }
        }
    }

    @FXML
    private void actionQuitter() throws IOException {
        Stage stage = (Stage) buttonQuitter.getScene().getWindow();
        stage.close();
    }

    @FXML
    private void getConfiguration() throws IOException {

        String csvFilePath = "./configuration.csv";

        // Les Données
        String urlConfig = urlField.getText();
        String portConfig = portField.getText();
        String alertFileConfig = alertFileField.getText();
        String dataFileConfig = dataFileField.getText();
        String salleConfig = topicsField.getText();
        String restDurationConfig = restDurationComboBox.getValue();
        String temperatureConfig = temperatureTextField.getText();
        String humidityConfig = humidityTextField.getText();
        String co2Config = co2TextField.getText();
        String activityConfig = activityTextField.getText();
        String tvocConfig = tvocTextField.getText();
        String illuminationConfig = illuminationTextField.getText();
        String infraredConfig = infraredTextField.getText();
        String infrared_and_visibleConfig = infrared_and_visibleTextField.getText();
        String pressureConfig = pressureTextField.getText();

        // Manque une/des information(s)
        if (urlConfig.isEmpty() || portConfig.isEmpty() || alertFileConfig.isEmpty() || dataFileConfig.isEmpty() ||
                salleConfig.isEmpty() || restDurationConfig.isEmpty() || temperatureConfig.isEmpty()
                || humidityConfig.isEmpty() || co2Config.isEmpty() ||
                activityConfig.isEmpty() || tvocConfig.isEmpty() || illuminationConfig.isEmpty() ||
                infraredConfig.isEmpty() || infrared_and_visibleConfig.isEmpty() || pressureConfig.isEmpty()) {

            Alert missedAlert = new Alert(AlertType.ERROR);

            missedAlert.setTitle("Error alert");
            missedAlert.setHeaderText("Il manque une/des information(s) dans la configuration");
            missedAlert.setContentText("Vérifiez tous les champs !");

            missedAlert.showAndWait();
        }

        try (BufferedWriter writer = new BufferedWriter(new FileWriter(csvFilePath))) {

            // Écris les données dans le fichier CSV :

            // url
            writer.write("url: \"");
            writer.write(String.format("%s", urlConfig));
            writer.write("\"\n");

            // port
            writer.write("port: ");
            writer.write(String.format("%s\n", portConfig));

            // keepalive
            writer.write("keepalive: 60\n");

            // dataFile
            writer.write("dataFile: \"");
            writer.write(String.format("%s", dataFileConfig));
            writer.write("\"\n");

            // alertFile
            writer.write("alertFile: \"");
            writer.write(String.format("%s", alertFileConfig));
            writer.write("\"\n");

            // topics
            String[] topics = salleConfig.split(",");
            writer.write("topics: [");
            for (int i = 0; i < topics.length; i++) {
                // remove spaces
                topics[i] = topics[i].trim();
                writer.write(String.format("\"%s\"", topics[i]));
                if (i < topics.length - 1) {
                    writer.write(",");
                }
            }
            writer.write("]\n");

            // selectedData
            writer.write(
                    "selectedData: [\"temperature\",\"humidity\",\"co2\",\"activity\",\"tvoc\",\"illumination\",\"infrared\",\"infrared_and_visible\",\"pressure\"]\n");

            // rest_duration
            writer.write("rest_duration  : ");
            writer.write(String.format("%s\n", restDurationConfig));

            // running_time
            writer.write("running_time : 10\n");

            // thresholds
            writer.write("thresholds:\n");

            // limites
            writer.write("  temperature : ");
            writer.write(String.format("%s\n", temperatureConfig));

            writer.write("  humidity : ");
            writer.write(String.format("%s\n", humidityConfig));

            writer.write("  co2 : ");
            writer.write(String.format("%s\n", co2Config));

            writer.write("  activity : ");
            writer.write(String.format("%s\n", activityConfig));

            writer.write("  tvoc : ");
            writer.write(String.format("%s\n", tvocConfig));

            writer.write("  illumination : ");
            writer.write(String.format("%s\n", illuminationConfig));

            writer.write("  infrared : ");
            writer.write(String.format("%s\n", infraredConfig));

            writer.write("  infrared_and_visible : ");
            writer.write(String.format("%s\n", infrared_and_visibleConfig));

            writer.write("  pressure : ");
            writer.write(String.format("%s\n", pressureConfig));

        } catch (IOException e) {
            e.printStackTrace();
        }

        // Créer le fichier configuation en yaml
        if (!fileNewConfigFile.exists()) {
            Files.copy(fileOldConfigFile.toPath(), fileNewConfigFile.toPath());
        } else {
            Files.delete(fileNewConfigFile.toPath());
            Files.copy(fileOldConfigFile.toPath(), fileNewConfigFile.toPath());
        }

        if (!urlConfig.isEmpty() && !portConfig.isEmpty() && !alertFileConfig.isEmpty() && !dataFileConfig.isEmpty() &&
                !salleConfig.isEmpty() && !restDurationConfig.isEmpty() && !temperatureConfig.isEmpty()
                && !humidityConfig.isEmpty() &&
                !co2Config.isEmpty() && !activityConfig.isEmpty() && !tvocConfig.isEmpty()
                && !illuminationConfig.isEmpty() &&
                !infraredConfig.isEmpty() && !infrared_and_visibleConfig.isEmpty() && !pressureConfig.isEmpty()) {


            // Créer et démarrer le thread pour le script Python
        Thread pythonThread = new Thread(() -> {
            try {
                // Commande complète à exécuter
                String command = "python3 ./sae-iot.py";

                // Créer le processus
                ProcessBuilder processBuilder = new ProcessBuilder(command.split("\\s+"));
                processBuilder.redirectErrorStream(true);

                // Exécuter la commande
                Process process = processBuilder.start();

                // Lire la sortie du processus
                BufferedReader reader = new BufferedReader(new InputStreamReader(process.getInputStream()));
                String line;
                while ((line = reader.readLine()) != null) {
                    System.out.println(line);
                }

                // Attendre que le processus se termine
                int exitCode = process.waitFor();
                System.out.println("La commande s'est terminée avec le code de sortie : " + exitCode);

            } catch (IOException | InterruptedException e) {
                e.printStackTrace();
            }
        });

        // Démarrer le thread Python
        pythonThread.start();
                    
            Main.setRoot("select");

        }

    }

}