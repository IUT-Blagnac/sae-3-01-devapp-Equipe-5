package com.malyart.tools;

public class SelectSalle {
    private static SelectSalle instance = new SelectSalle();

    private String selectedOption;

    private SelectSalle() {
    }

    public static SelectSalle getInstance() {
        return instance;
    }

    public String getSelectedOption() {
        return selectedOption;
    }

    public void setSelectedOption(String selectedOption) {
        this.selectedOption = selectedOption;
    }
}
