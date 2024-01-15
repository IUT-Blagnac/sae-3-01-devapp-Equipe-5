package com.malyart.views;

import java.io.FileReader;
import java.io.IOException;
import java.util.List;
import java.util.Timer;
import java.util.TimerTask;

import com.malyart.controls.Main;
import com.malyart.tools.SelectSalle;
import com.opencsv.CSVReader;
import com.opencsv.exceptions.CsvException;

import javafx.application.Platform;
import javafx.beans.property.SimpleListProperty;
import javafx.beans.property.SimpleStringProperty;
import javafx.collections.FXCollections;
import javafx.collections.ObservableList;
import javafx.fxml.FXML;
import javafx.scene.control.Button;
import javafx.scene.control.Label;
import javafx.scene.control.TableColumn;
import javafx.scene.control.TableView;
import javafx.stage.Stage;

public class DisplayDataController {

    private String selectedOption = SelectSalle.getInstance().getSelectedOption();
    private String targetRoom = selectedOption;
    private String csvData = "./data.csv";

    @FXML
    private Label labelSalle;
    @FXML
    private Button buttonQuitter;
    @FXML
    private Button buttonTextVersion;

    @FXML
    private TableView<Room> tableView;
    @FXML
    private TableColumn<Room, String> timeColumn;
    @FXML
    private TableColumn<Room, String> temperatureColumn;
    @FXML
    private TableColumn<Room, String> humidityColumn;
    @FXML
    private TableColumn<Room, String> co2Column;
    @FXML
    private TableColumn<Room, String> activityColumn;
    @FXML
    private TableColumn<Room, String> tvocColumn;
    @FXML
    private TableColumn<Room, String> illuminationColumn;
    @FXML
    private TableColumn<Room, String> infraredColumn;
    @FXML
    private TableColumn<Room, String> infrared_and_visibleColumn;
    @FXML
    private TableColumn<Room, String> pressureColumn;

    private Timer refreshTimer;


    private static class Room {
        private SimpleStringProperty time;
        private SimpleStringProperty temperature;
        private SimpleStringProperty humidity;
        private SimpleStringProperty co2;
        private SimpleStringProperty activity;
        private SimpleStringProperty tvoc;
        private SimpleStringProperty illumination;
        private SimpleStringProperty infrared;
        private SimpleStringProperty infrared_and_visible;
        private SimpleStringProperty pressure;

        private Room(String time, String temperature, String humidity, String co2, String activity, String tvoc, String illumination,
                String infrared, String infrared_and_visible, String pressure) {
            this.time = new SimpleStringProperty(time);
            this.temperature = new SimpleStringProperty(temperature);
            this.humidity = new SimpleStringProperty(humidity);
            this.co2 = new SimpleStringProperty(co2);
            this.activity = new SimpleStringProperty(activity);
            this.tvoc = new SimpleStringProperty(tvoc);
            this.illumination = new SimpleStringProperty(illumination);
            this.infrared = new SimpleStringProperty(infrared);
            this.infrared_and_visible = new SimpleStringProperty(infrared_and_visible);
            this.pressure = new SimpleStringProperty(pressure);
        }

        public SimpleStringProperty timeProperty() {
            return time;
        }

        public SimpleStringProperty temperatureProperty() {
            return temperature;
        }

        public SimpleStringProperty humidityProperty() {
            return humidity;
        }

        public SimpleStringProperty co2Property() {
            return co2;
        }

        public SimpleStringProperty activityProperty() {
            return activity;
        }

        public SimpleStringProperty tvocProperty() {
            return tvoc;
        }

        public SimpleStringProperty illuminationProperty() {
            return illumination;
        }

        public SimpleStringProperty infraredProperty() {
            return infrared;
        }

        public SimpleStringProperty infrared_and_visibleProperty() {
            return infrared_and_visible;
        }

        public SimpleStringProperty pressureProperty() {
            return pressure;
        }
    }

    
    /*
     * Initialisation de la fenêtre
     * - Affiche la salle sélectionnée
     * - Démarre le TimerTask
     * - Affiche toutes les valeurs captées à plusieurs temps de la salle sélectionnée (tableau)
     */
    @FXML
    public void initialize() {
        labelSalle.setText(targetRoom);

        timeColumn.setCellValueFactory(cellData -> cellData.getValue().timeProperty());
        temperatureColumn.setCellValueFactory(cellData -> cellData.getValue().temperatureProperty());
        humidityColumn.setCellValueFactory(cellData -> cellData.getValue().humidityProperty());
        co2Column.setCellValueFactory(cellData -> cellData.getValue().co2Property());
        activityColumn.setCellValueFactory(cellData -> cellData.getValue().activityProperty());
        tvocColumn.setCellValueFactory(cellData -> cellData.getValue().tvocProperty());
        illuminationColumn.setCellValueFactory(cellData -> cellData.getValue().illuminationProperty());
        infraredColumn.setCellValueFactory(cellData -> cellData.getValue().infraredProperty());
        infrared_and_visibleColumn.setCellValueFactory(cellData -> cellData.getValue().infrared_and_visibleProperty());
        pressureColumn.setCellValueFactory(cellData -> cellData.getValue().pressureProperty());

        // Charger des données initiales à partir du fichier CSV
        ObservableList<Room> donnees = chargerData();
        SimpleListProperty<Room> listeDonnees = new SimpleListProperty<>(donnees);
        tableView.itemsProperty().bindBidirectional(listeDonnees);

        // Initialiser Timer et TimerTask
        refreshTimer = new Timer(true);
        TimerTask refreshTask = new TimerTask() {
            @Override
            public void run() {
                Platform.runLater(() -> {
                    ObservableList<Room> nouvellesDonnees = chargerData();
                    listeDonnees.setAll(nouvellesDonnees);
                });
            }
        };

        // Démarrer TimerTask toutes les 5000 millisecondes (5 secondes)
        refreshTimer.scheduleAtFixedRate(refreshTask, 0, 5000);
    }


    /**
     * Rafraîchir les données
     * - Réinitialiser les valeurs
     * - Lire le fichier data.csv
     * - Afficher les nouvelles valeurs
     * 
     * @throws IOException
     * @throws NumberFormatException
     * @throws CsvException
     */
    private ObservableList<Room> chargerData() {
        ObservableList<Room> room = FXCollections.observableArrayList();

        try (CSVReader reader = new CSVReader(new FileReader(csvData))) {
            List<String[]> dataLignes = reader.readAll();
            for (String[] dataLigne : dataLignes) {
                if (dataLigne.length > 0 && dataLigne[0].equals(targetRoom)) {
                    room.add(new Room(dataLigne[1], dataLigne[2], dataLigne[3], dataLigne[4], dataLigne[5],
                            dataLigne[6], dataLigne[7], dataLigne[8], dataLigne[9], dataLigne[10]));
                }
            }
        } catch (IOException | NumberFormatException | CsvException e) {
            e.printStackTrace();
        }

        return room;
    }

    /**
     * Ouvrir la version texte (les dernières données captées)
     * 
     * @throws IOException
     */
    @FXML
    private void openTextVersion() throws IOException {
        Main.changeScene("display");
    }

    /**
     * Fermer la fenêtre
     * 
     * @throws IOException
     */
    @FXML
    private void actionQuitter() throws IOException {
        Stage stage = (Stage) buttonQuitter.getScene().getWindow();
        stage.close();
    }
}
