package com.malyart;

import java.io.BufferedReader;
import java.io.File;
import java.io.FileInputStream;
import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;

import javafx.application.Platform;
import javafx.fxml.FXML;
import javafx.scene.control.Alert;
import javafx.scene.control.Alert.AlertType;
import javafx.scene.control.Button;
import javafx.scene.control.ChoiceBox;
import javafx.scene.control.ToggleButton;

public class SelectController {

    @FXML
    private Button buttonQuitter;
    @FXML
    private Button buttonDisplay;
    @FXML
    private Button buttonConfigure;
    @FXML
    private ChoiceBox<String> listSalle;
    @FXML
    private ToggleButton toggleButtonLaunchScript;

    private static Thread pythonThread;
    private static Process pythonProcess;

    @FXML
    private void initialize() {

        File configExists = new File("./configuration.csv");

        String ligne;

        try {

            InputStream ips = new FileInputStream(configExists);
            InputStreamReader ipsr = new InputStreamReader(ips);
            BufferedReader br = new BufferedReader(ipsr);

            while ((ligne = br.readLine()) != null) {
                String valeur = "";

                // Suite de conditions pour remplir les champs automatiquement

                if (ligne.startsWith("topics: ")) {
                    valeur = ligne.replace("topics: [", "");
                    valeur = valeur.replace("AM107/by-room/", "");
                    valeur = valeur.replace("/data", "");
                    valeur = valeur.replace("]", "");
                    valeur = valeur.replace("\"", "");

                    // Diviser la chaîne en éléments individuels
                    String[] elements = valeur.split(",");
                    listSalle.getItems().addAll(elements);

                }
            }

            br.close();

            toggleButtonLaunchScript.setOnAction(event -> {
                if (toggleButtonLaunchScript.isSelected()) {
                    // Button is selected, start the Python thread
                    startPythonThread();
                } else {
                    // Button is not selected, stop the Python thread
                    stopPythonThread();
                }
            });

        } catch (Exception e) {
            System.out.println(e.toString());
        }

        // Ajouter un écouteur sur la ChoiceBox pour mettre à jour le modèle partagé
        listSalle.getSelectionModel().selectedItemProperty().addListener((observable, oldValue, newValue) -> {
            SelectSalle.getInstance().setSelectedOption(newValue);
        });

    }

    private void startPythonThread() {
        pythonThread = new Thread(() -> {
            try {
                // Commande complète à exécuter
                String command = "python3 ./sae-iot.py";

                // Créer le processus
                ProcessBuilder processBuilder = new ProcessBuilder(command.split("\\s+"));
                processBuilder.redirectErrorStream(true);
                pythonProcess = processBuilder.start();

                // Wait for the Python process to finish
                pythonProcess.waitFor();

            } catch (Exception e) {
                e.printStackTrace();
            }
        });

        // Start the Python thread
        pythonThread.start();
    }

    private void stopPythonThread() {
        if (pythonThread != null && pythonThread.isAlive()) {
            // Stop the Python script gracefully
            if (pythonProcess != null) {
                pythonProcess.destroy();
            }
        }
    }

    @FXML
    private void switchToConfigure() throws IOException {
        stopPythonThread();
        Main.setRoot("configure");
    }

    @FXML
    private void openDisplay() throws IOException {
        if (listSalle.getSelectionModel().isEmpty()) {
            Alert missedAlert = new Alert(AlertType.ERROR);

            missedAlert.setTitle("Error alert");
            missedAlert.setHeaderText("Choisissez une salle !");

            missedAlert.showAndWait();
        } else {
            Main.openNewWindow("display");
        }
    }

    @FXML
    private void actionQuitter() throws IOException {
        stopPythonThread();
        Platform.exit();
    }
}
