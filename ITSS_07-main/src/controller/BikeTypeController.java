package com.itss.controller;


import com.itss.model.BikeTypeModel;
import com.itss.object.BikeType;
import javafx.collections.ObservableList;

import java.sql.SQLException;
import java.util.ArrayList;
import java.util.List;

public class BikeTypeController {
    private BikeTypeModel bikeTypeModel;
    public BikeTypeController() { this.bikeTypeModel = new BikeTypeModel(); }

    public List<BikeType> getTypes() {
        List<BikeType> list = new ArrayList<>();
        try {
            list = bikeTypeModel.getTypes();
        } catch (SQLException ex) {
            System.err.println("Error: " + ex.toString());
        }
        return list;
    }

    public int insertBikeType(int id, String name,String description,Boolean electricType) {
        int cnt = 0;
        try {
            cnt = bikeTypeModel.insertBikeType(id,name,electricType,description);
        } catch (SQLException ex) {
            System.err.println("Error: " + ex.toString());
        }
        return cnt;
    }

    public void getTypesToTable(ObservableList<BikeType> data) throws SQLException {
        bikeTypeModel.getTypesToTable(data);
    }
    
    public int deleteBikeType(int id) {
        int cnt = 0;
        try {
            cnt = bikeTypeModel.deleteBikeType(id);
        } catch (SQLException ex) {
            System.err.println("Error: " + ex.toString());
        }
        return cnt;
    }
}
