package view.User.ReturnBikeScreen;

import controller.BikeController;
import controller.PriceController;
import controller.TransactionController;
import javafx.event.ActionEvent;
import javafx.fxml.FXML;
import javafx.fxml.FXMLLoader;
import javafx.scene.Node;
import javafx.scene.Parent;
import javafx.scene.Scene;
import javafx.scene.control.Alert;
import javafx.scene.control.Alert.AlertType;
import javafx.scene.control.Label;
import javafx.scene.control.TextField;
import javafx.stage.Stage;
import BankSystem.Bank;
import object.Bike;
import object.Price;
import object.Transaction;

import java.io.IOException;
import java.sql.SQLException;
import java.text.SimpleDateFormat;
import java.util.Date;
import java.util.List;

public class CheckoutController {
    @FXML
    private Label customerLabel;
    
    @FXML
    private Label customerNameLabel;
    
    @FXML
    private Label amountLabel;

    @FXML
    private Label depositLabel;

    @FXML
    private Label timeLabel2;

    @FXML
    private Label errorMessage;

    @FXML
    private Label bikeLabel;

    @FXML
    private Label paymentMethod;

    @FXML
    private Label timeLabel;

    @FXML
    private TextField cardIDField;

    TransactionController transactionController = new TransactionController();
    private Transaction tr = null;
    private int parkingLotID;
    private int bikeID;
    private double rentalTime = 0;
    private double rentalFee = 0;
    Bank banksystem = new Bank();

    PriceController priceController = new PriceController();

    public void initData(int parkingLotID, int bikeID) throws SQLException {
        this.bikeID = bikeID;
        this.parkingLotID = parkingLotID;
        this.tr = transactionController.getRentalTransaction(bikeID);
        BikeController bikeController = new BikeController();
        String bikeType = bikeController.getBikeById(bikeID).getType();
        if (tr == null) {
            errorMessage.setText("Conflict Error!");
            errorMessage.setVisible(true);
        } else {
        	String cardID = this.tr.getCardID();
            String userName = banksystem.getUserName(cardID);
            if (userName != null) {
                customerNameLabel.setText(userName);
            }
//            customerLabel.setText(this.tr.getCardID());
            bikeLabel.setText("ID" + this.tr.getBikeID() + " - " + this.tr.getBikeName());
            timeLabel.setText(this.tr.getDate());
            this.rentalTime = this.tr.getRentingTime() + timeCalculate(tr.getUnlockDate());
            timeLabel2.setText(this.rentalTime + " minute(s)");
            depositLabel.setText(Float.toString(this.tr.getDeposit()) + " dong");
            this.rentalFee = feeCalculate(this.rentalTime, this.tr.getPaymentMethod(), bikeType);
            amountLabel.setText(this.rentalFee + " dong");
            switch (this.tr.getPaymentMethod()) {
                case 0:
                    paymentMethod.setText("Hour");
                    break;
                case 1:
                    paymentMethod.setText("Day");
                    break;
                default:
                    paymentMethod.setText("null");
                    break;
            }
        }
    }

    private double timeCalculate(String date) {
        try {
            Date dateBegin = new SimpleDateFormat("dd-M-yyyy HH:mm:ss").parse(date);
            long begin = dateBegin.getTime();
            long end = new Date().getTime();
            return (double) Math.ceil((float) (end - begin) / (1000 * 60) * 10) / 10; // tinh theo phut
        } catch (Exception ex) {
            System.err.println("Error: " + ex.toString());
        }
        return 0;
    }

    public double feeCalculate(double minutes, int paymentMethod, String bikeType) {
        double first30minute = 0;
        double per15minute = 0;
        double perDay = 0;
        double per1Hour = 0;
        double latePer15minute = 0;

        List<Price> priceList = priceController.getPrices();
        if (priceList != null && priceList.size() >= 5) {
            first30minute = priceList.get(0).getPrice();
            per15minute = priceList.get(1).getPrice();
            perDay = priceList.get(2).getPrice();
            per1Hour = priceList.get(3).getPrice();
            latePer15minute = priceList.get(4).getPrice();
        }
        
        double feeMultiplier = 1.0; // Default is 1.0 (no fee change)

        // Check type bike
        if (!"Standard bike".equals(bikeType)) {
            // If not a Standard Bike, multiply by 1.5 to the fee
            feeMultiplier = 1.5;
        }

        switch (paymentMethod) {
            case 0:
                if (minutes <= 10.0) return 0;
                if (minutes <= 30.0) return first30minute * feeMultiplier;
                double fee = first30minute * feeMultiplier;
                minutes -= 30;
                while (minutes > 0) {
                    fee += per15minute * feeMultiplier;
                    minutes -= 15;
                }
                return fee;
            case 1:
                if (minutes < 12 * 60) {
                    return (perDay - (12 - Math.ceil(minutes / 60)) * per1Hour) * feeMultiplier;
                } else if (minutes <= 24 * 60) {
                    return perDay * feeMultiplier;
                } else {
                    return (perDay + latePer15minute * Math.ceil((minutes - 24 * 60) / 15)) * feeMultiplier;
                }
            default:
                return 0;
        }
    }


    @FXML
    void checkout(ActionEvent event) throws Exception {
        String enteredCardID = cardIDField.getText();
        String customerCardID = tr.getCardID();
        cardIDField.clear();
        boolean result = banksystem.getCard(enteredCardID);

        if (!enteredCardID.equals(customerCardID)) {
            Alert alert = new Alert(AlertType.ERROR);
            alert.setTitle("Error");
            alert.setHeaderText(null);
            alert.setContentText("The entered card does not match the customer's card.");
            alert.showAndWait();
            return;
        }

        if (result == true) {
            float amount = banksystem.getAmount(enteredCardID);
            if (amount + tr.getDeposit() > this.rentalFee) {
                float addmoney = (float) (tr.getDeposit() - this.rentalFee) + amount;
                banksystem.updateAmount(enteredCardID, addmoney);
                // update bike's status
                BikeController bikeController = new BikeController();
                System.out.println(this.parkingLotID);
                bikeController.updateStatus(this.bikeID, this.parkingLotID, false);

                // update transaction
                transactionController.updateStatus(this.tr.getId(), true);
                errorMessage.setVisible(false);
                Alert a = new Alert(Alert.AlertType.INFORMATION);
                a.setTitle("Payment success!");
                a.setHeaderText(null);
                a.setHeight(100);
                a.setContentText("Payment success. Back to home screen");
                a.initOwner((Stage) ((Node) event.getSource()).getScene().getWindow());
                a.show();
                try {
                    Thread.sleep(1000);
                    this.backToUserScreen(event);
                } catch (Exception ex) {
                    System.err.println("Error: " + ex.toString());
                }
                return;
            } else {
                errorMessage.setText("Not enough money to make the transaction");
                errorMessage.setVisible(true);
                return;
            }
        } else {
            errorMessage.setText("The card does not exist or the card code is incorrect");
            errorMessage.setVisible(true);
            return;
        }
    }

    @FXML
    public void backToUserScreen(ActionEvent event) throws IOException {
        Parent userScr = FXMLLoader.load(getClass().getResource("../UserScreen.fxml"));
        Scene userScene = new Scene(userScr);

        Stage window = (Stage) ((Node) event.getSource()).getScene().getWindow();

        window.setScene(userScene);
        window.show();
    }
}
