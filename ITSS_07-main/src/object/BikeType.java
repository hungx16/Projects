package com.itss.object;

public class BikeType {
    private int id;
    private String name;

    private String description;

    private Boolean electricType;

    public int getId() {
        return id;
    }

    public void setId(int id) {
        this.id = id;
    }

    public String getName() {
        return name;
    }

    public void setName(String name) {
        this.name = name;
    }

    public Boolean getElectricType() {
        return electricType;
    }

    public void setElectricType(Boolean electricType) {
        this.electricType = electricType;
    }

    public String getDescription() {
        return description;
    }

    public void setDescription(String description) {
        this.description = description;
    }
}
