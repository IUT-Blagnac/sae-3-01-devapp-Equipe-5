package com.malyart;

import java.io.FileReader;
import java.io.IOException;
import java.util.List;

import com.opencsv.CSVReader;
import com.opencsv.exceptions.CsvException;

import javafx.fxml.FXML;
import javafx.scene.control.Button;
import javafx.scene.control.Label;
import javafx.scene.control.TextArea;
import javafx.stage.Stage;

public class DisplayController {

    // Récupérer la valeur depuis le modèle partagé et l'afficher dans un Label
    private String selectedOption = DataModel.getInstance().getSelectedOption();

    @FXML
    private Label labelSalle;
    @FXML
    private Button buttonQuitter;
    @FXML
    private TextArea temperatureTextArea;
    @FXML
    private TextArea humidityTextArea;
    @FXML
    private TextArea co2TextArea;
    @FXML
    private TextArea activityTextArea;
    @FXML
    private TextArea tvocTextArea;
    @FXML
    private TextArea illuminationTextArea;
    @FXML
    private TextArea infraredTextArea;
    @FXML
    private TextArea infrared_and_visibleTextArea;
    @FXML
    private TextArea pressureTextArea;

    @FXML
    private void switchToConfigure() throws IOException {
        Main.setRoot("configure");
    }

    @FXML
    private void actionQuitter() throws IOException {
        Stage stage = (Stage) buttonQuitter.getScene().getWindow();
        stage.close();
    }

    public void initialize() throws CsvException {
        labelSalle.setText(selectedOption);

        String csvFilePath = "./data.csv";
        String targetRoom = selectedOption;

            try (CSVReader reader = new CSVReader(new FileReader(csvFilePath))) {
                List<String[]> records = reader.readAll();

                for (String[] record : records) {
                    if (record.length > 0 && record[0].equals(targetRoom)) {
                        // La première colonne correspond à la salle
                        double temperature = Double.parseDouble(record[2]);
                        double humidity = Double.parseDouble(record[3]);
                        int co2 = Integer.parseInt(record[4]);
                        int activity = Integer.parseInt(record[5]);
                        int tvoc = Integer.parseInt(record[6]);
                        int illumination = Integer.parseInt(record[7]);
                        int infrared = Integer.parseInt(record[8]);
                        int infraredAndVisible = Integer.parseInt(record[9]);
                        double pressure = Double.parseDouble(record[10]);

                        // Afficher les valeurs
                        temperatureTextArea.setText(String.valueOf(temperature));
                        humidityTextArea.setText(String.valueOf(humidity));
                        co2TextArea.setText(String.valueOf(co2));
                        activityTextArea.setText(String.valueOf(activity));
                        tvocTextArea.setText(String.valueOf(tvoc));
                        illuminationTextArea.setText(String.valueOf(illumination));
                        infraredTextArea.setText(String.valueOf(infrared));
                        infrared_and_visibleTextArea.setText(String.valueOf(infraredAndVisible));
                        pressureTextArea.setText(String.valueOf(pressure));

                        break; // Sortez de la boucle après avoir trouvé la salle
                    }
                }
            } catch (IOException | NumberFormatException e) {
                e.printStackTrace();
            }
        
    }

}
