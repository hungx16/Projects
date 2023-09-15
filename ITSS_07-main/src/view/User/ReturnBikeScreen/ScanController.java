package view.User.ReturnBikeScreen;

import controller.BikeController;
import javafx.event.ActionEvent;
import javafx.fxml.FXML;
import javafx.fxml.FXMLLoader;
import javafx.scene.Parent;
import javafx.scene.control.Label;
import javafx.scene.control.TextField;
import javafx.scene.layout.BorderPane;

import java.io.IOException;
import java.sql.SQLException;

public class ScanController {

    @FXML
    private TextField idField;

    @FXML
    private Label errorMessage;

    private int parkingLotID;
    private BorderPane parentPane;
    BikeController bikeController = new BikeController();

    public void initData(int parkingLotID, BorderPane parentPane) {
        this.parkingLotID = parkingLotID;
        this.parentPane = parentPane;
    }

    public void checkBikeID(ActionEvent event) throws SQLException {
        if (idField.getText().isEmpty()) {
            errorMessage.setText("Please enter bike code");
            errorMessage.setVisible(true);
            return;
        }
        int bikeID;
        try {
            bikeID = Integer.parseInt(idField.getText());
        } catch (final NumberFormatException e) {
            errorMessage.setText("The bike code contains only numeric characters!");
            errorMessage.setVisible(true);
            return;
        }
        idField.clear();

        if (bikeController.checkStatus(bikeID)) {
            try {
                this.goToCheckout(bikeID);
            } catch (IOException ex) {
                System.err.println("Error: " + ex.toString());
            }
        } else {
            errorMessage.setText("Bike code does not exist or is still in the parking lot");
            errorMessage.setVisible(true);
        }
    }

    private void goToCheckout(int bikeID) throws IOException, SQLException{
        FXMLLoader loader = new FXMLLoader();
        loader.setLocation(getClass().getResource("Checkout.fxml"));
        Parent scanScreen = loader.load();

        CheckoutController controller = loader.getController();
        controller.initData(this.parkingLotID, bikeID);

        this.parentPane.setCenter(scanScreen);
    }
}
