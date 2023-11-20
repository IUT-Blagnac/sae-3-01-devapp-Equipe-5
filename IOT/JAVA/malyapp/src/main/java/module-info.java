module com.malyart {
    requires javafx.controls;
    requires javafx.fxml;

    opens com.malyart to javafx.fxml;
    exports com.malyart;
}
