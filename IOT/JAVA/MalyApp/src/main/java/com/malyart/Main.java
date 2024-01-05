package com.malyart;

import javafx.application.Application;
import javafx.fxml.FXMLLoader;
import javafx.scene.Parent;
import javafx.scene.Scene;
import javafx.stage.Stage;

import java.io.IOException;

/**
 * JavaFX App
 */
public class Main extends Application {

    private static Scene scene;

    @Override
    public void start(Stage stage) throws IOException {
        scene = new Scene(loadFXML("select"), 900, 700);
        stage.setScene(scene);
        stage.setTitle("MALYAPP");
        stage.show();
        stage.setResizable(false);
    }

    static void setRoot(String fxml) throws IOException {
        scene.setRoot(loadFXML(fxml));
    }

    static void openNewWindow(String fxml) throws IOException {
        Stage newStage = new Stage();
        Scene newScene = new Scene(loadFXML(fxml), 900, 700);
        newStage.setScene(newScene);
        newStage.setTitle("MALYAPP");
        newStage.show();
        newStage.setResizable(false);
    }

    private static Parent loadFXML(String fxml) throws IOException {
        FXMLLoader fxmlLoader = new FXMLLoader(Main.class.getResource(fxml + ".fxml"));
        return fxmlLoader.load();
    }

    public static void main(String[] args) {
        launch();
    }

}