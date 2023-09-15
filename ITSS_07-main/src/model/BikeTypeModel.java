package com.itss.model;


import com.itss.object.BikeType;
import javafx.collections.ObservableList;

import java.sql.*;
import java.util.ArrayList;
import java.util.List;

public class BikeTypeModel {
    private static Connection getConnection() throws Exception {
        String url = "jdbc:mysql://localhost:3306/EcoBikeRental";  // your db name
        String user = "root1";                                                         // your db username
        String password = "Hoang&14122002";                                                       // your db password
        return DriverManager.getConnection(url, user, password);
    }

    public void getTypesToTable(ObservableList<BikeType> data) throws SQLException {
        try {
            Class.forName ("com.mysql.jdbc.Driver");
        } catch (ClassNotFoundException e) {
            e.printStackTrace();
        }
        Connection con = null;
        PreparedStatement ps = null;
        ResultSet rs = null;
        try {
            con = this.getConnection();
            ps = con.prepareStatement("SELECT * FROM BikeType\n");
            rs = ps.executeQuery();

            while (rs.next()) {
                BikeType bikeType = bikeTypeMapper(rs);
                data.add(bikeType);
            }
        } catch (Exception e) {
            System.out.println(e);
        } finally {
            if (rs != null) {
                rs.close();
            }
            if (ps != null) {
                ps.close();
            }
            if (con != null) {
                con.close();
            }
        }
    }

    public List<BikeType> getTypes() throws SQLException {
        List<BikeType> list = new ArrayList<>();
        Connection con = null;
        PreparedStatement ps = null;
        ResultSet rs = null;
        try {
            con = this.getConnection();
            ps = con.prepareStatement("SELECT * FROM BikeType\n");
            rs = ps.executeQuery();

            while (rs.next()) {
                BikeType bikeType = bikeTypeMapper(rs);
                list.add(bikeType);
            }
        } catch (Exception e) {
            System.out.println(e);
        } finally {
            if (rs != null) {
                rs.close();
            }
            if (ps != null) {
                ps.close();
            }
            if (con != null) {
                con.close();
            }
        }
        return list;
    }

    public int insertBikeType(int biketypeid, String name,Boolean electricType,String description) throws SQLException {
        Connection c = null;
        PreparedStatement ps = null;
        int cnt = 0;
        try {
            c = this.getConnection();
            ps = c.prepareStatement(
                    "INSERT INTO BikeType VALUES (?, ?,?,?)");
            ps.setString(2, name);
            ps.setInt(1, biketypeid);
            ps.setString(3,description);
            ps.setBoolean(4,electricType);

            cnt = ps.executeUpdate();
        } catch (Exception e) {
            e.printStackTrace();
        } finally {
            if (ps != null) {
                ps.close();
            }
            if (c != null) {
                c.close();
            }
        }
        return cnt;
    }
    public int deleteBikeType(int biketypeid) throws SQLException {
        Connection c = null;
        PreparedStatement ps = null;
        int cnt = 0;
        try {
            c = this.getConnection();
            ps = c.prepareStatement("DELETE FROM BikeType WHERE BikeTypeID = ?");
            ps.setInt(1, biketypeid);
            cnt = ps.executeUpdate();
        } catch (Exception e) {
            e.printStackTrace();
        } finally {
            if (ps != null) {
                ps.close();
            }
            if (c != null) {
                c.close();
            }
        }
        return cnt;
    }

    public static BikeType bikeTypeMapper(ResultSet rs) throws SQLException {
        BikeType bikeType = new BikeType();
        bikeType.setId(rs.getInt("BikeTypeID"));
        bikeType.setElectricType(rs.getBoolean("ElectricType"));
        bikeType.setDescription(rs.getString("Description"));
        bikeType.setName(rs.getString("Name"));
        return bikeType;
    }
}
