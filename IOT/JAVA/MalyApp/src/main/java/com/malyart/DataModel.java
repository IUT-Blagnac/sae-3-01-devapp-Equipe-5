package com.malyart;

public class DataModel {
    private static DataModel instance = new DataModel();

    private String selectedOption;

    private DataModel() {
    }

    public static DataModel getInstance() {
        return instance;
    }

    public String getSelectedOption() {
        return selectedOption;
    }

    public void setSelectedOption(String selectedOption) {
        this.selectedOption = selectedOption;
    }
}
