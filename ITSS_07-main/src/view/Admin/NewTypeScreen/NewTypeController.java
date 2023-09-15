package com.itss.view.Admin.NewTypeScreen;

import com.itss.controller.BikeTypeController;
import com.itss.model.BikeTypeModel;
import com.itss.object.BikeType;
import com.itss.view.AlertBox;
import javafx.collections.FXCollections;
import javafx.collections.ObservableList;
import javafx.event.ActionEvent;
import javafx.fxml.FXML;
import javafx.fxml.Initializable;
import javafx.scene.Node;
import javafx.scene.control.*;
import javafx.scene.control.cell.PropertyValueFactory;
import javafx.stage.Stage;

import java.io.IOException;
import java.net.URL;
import java.sql.SQLException;
import java.util.ResourceBundle;

public class NewTypeController implements Initializable {

    BikeTypeController bikeTypeController = new BikeTypeController();

    @FXML
    private TableView<BikeType> table;
    @FXML
    private TableColumn<BikeType, Integer> id;
    @FXML
    private TableColumn<BikeType, String> name;
    @FXML
    TextField input_id, input_name;
    @FXML
    private TextField input_description;
    @FXML
    private CheckBox input_type;
    @FXML
    private TableColumn<BikeType, String> description;

    @FXML
    private TableColumn<BikeType, String> electricType;

    ObservableList<BikeType> data = FXCollections.observableArrayList();

    public void addNewType(ActionEvent event) throws SQLException {
        String id = input_id.getText();
        String name = input_name.getText();
        String description = input_description.getText();
        Boolean electricType = input_type.isSelected();
        input_id.clear();
        input_name.clear();
        input_type.setSelected(false);
        input_description.clear();
        if(id.equals("") || name.equals("") || description.equals("")) {
            AlertBox.display("You have not entered full information !!");
        } else if(!isNumeric(id) || !onlyLettersSpaces(name)) {
            AlertBox.display("Invalid input !!");
        } else {
            int success = bikeTypeController.insertBikeType(Integer.parseInt(id),name,description,electricType);
            if (success==0) {
                Alert a = new Alert(Alert.AlertType.ERROR);
                a.setTitle("Failure!");
                a.setHeaderText(null);
                a.initOwner((Stage)((Node)event.getSource()).getScene().getWindow());
                a.show();
            } else {
                data.clear();
                getData();
                Alert a = new Alert(Alert.AlertType.INFORMATION);
                a.setTitle("Success!");
                a.setHeaderText(null);
                a.setContentText("Add new bike successfully !!");
                a.initOwner((Stage)((Node)event.getSource()).getScene().getWindow());
                a.show();
            }
        }
    }
    
    public void deleteType(ActionEvent event) throws SQLException, IOException {
        BikeType selectedItem = table.getSelectionModel().getSelectedItem();
        if (selectedItem != null) {
            int typeId = selectedItem.getId();

            int success = bikeTypeController.deleteBikeType(typeId);
            if (success == 0) {
                Alert a = new Alert(Alert.AlertType.ERROR);
                a.setTitle("Failure!");
                a.setHeaderText(null);
                a.initOwner((Stage) ((Node) event.getSource()).getScene().getWindow());
                a.show();
            } else {
                data.clear();
                getData();
                Alert a = new Alert(Alert.AlertType.INFORMATION);
                a.setTitle("Success!");
                a.setHeaderText(null);
                a.setContentText("Successfully deleted bike type");
                a.initOwner((Stage) ((Node) event.getSource()).getScene().getWindow());
                a.show();
            }
        } else {
            AlertBox.display("Select the type of bike to delete");
        }
    }

    public void getData() {
        try {
            bikeTypeController.getTypesToTable(data);
        } catch (SQLException throwables) {
            throwables.printStackTrace();
        }

        id.setCellValueFactory(new PropertyValueFactory<>("id"));
        name.setCellValueFactory(new PropertyValueFactory<>("name"));
        description.setCellValueFactory(new PropertyValueFactory<>("description"));
        electricType.setCellValueFactory(new PropertyValueFactory<>("electricType"));
        table.setItems(data);
    }

    @Override
    public void initialize(URL url, ResourceBundle resourceBundle) {
        getData();
    }

    public static boolean isNumeric(String strNum) {
        if (strNum == null) {
            return false;
        }
        try {
            double d = Double.parseDouble(strNum);
        } catch (NumberFormatException nfe) {
            return false;
        }
        return true;
    }

    public static boolean onlyLettersSpaces(String s){
        for(int i=0;i<s.length();i++){
            char ch = s.charAt(i);
            if (Character.isLetter(ch) || ch == ' ') {
                continue;
            }
            return false;
        }
        return true;
    }
}
