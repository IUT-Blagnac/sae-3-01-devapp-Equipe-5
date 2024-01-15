package com.malyart.views;

import java.io.BufferedReader;
import java.io.File;
import java.io.FileInputStream;
import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;

import com.malyart.controls.Main;
import com.malyart.tools.SelectSalle;

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

    /*
     * Initialisation de la fenêtre
     * - Remplir la ChoiceBox avec les salles congifurées
     */
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

                if (ligne.startsWith("topics: ")) {
                    valeur = ligne.replace("topics: [", "");
                    valeur = valeur.replace("AM107/by-room/", "");
                    valeur = valeur.replace("/data", "");
                    valeur = valeur.replace("]", "");
                    valeur = valeur.replace("\"", "");

                    // Diviser la chaîne en éléments individuels
                    String[] elements = valeur.split(",");
                    for (String element : elements) {
                        if (!element.equals("+")) {
                            listSalle.getItems().add(element);
                        }
                    }

                }
            }

            br.close();

            toggleButtonLaunchScript.setOnAction(event -> {
                if (toggleButtonLaunchScript.isSelected()) {
                    startPythonThread();
                } else {
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


    /*
     * Démarrer le script Python
     */
    private void startPythonThread() {
        pythonThread = new Thread(() -> {
            try {
                String command = "python3 ./sae-iot.py";

                ProcessBuilder processBuilder = new ProcessBuilder(command.split("\\s+"));
                processBuilder.redirectErrorStream(true);
                pythonProcess = processBuilder.start();

                pythonProcess.waitFor();

            } catch (Exception e) {
                e.printStackTrace();
            }
        });

        pythonThread.start();
    }


    /*
     * Arrêter le script Python
     */
    private void stopPythonThread() {
        if (pythonThread != null && pythonThread.isAlive()) {
            // Stop the Python script gracefully
            if (pythonProcess != null) {
                pythonProcess.destroy();
            }
        }
    }


    /**
     * Change la fenêtre vers la fenêtre de configuration
     * 
     * @throws IOException
     */
    @FXML
    private void switchToConfigure() throws IOException {
        stopPythonThread();
        Main.setRoot("configure");
    }


    /**
     * Ouvrir la fenêtre de visualisation
     * 
     * @throws IOException
     */
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
