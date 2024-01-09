Feature: MQTT Server Connection
  As a user of the storage condition monitoring application
  I want to connect to the MQTT server
  So that I can receive data from the sensors

  Scenario: Successfully establishing a connection to the MQTT server
    Given an available MQTT server
    When the client attempts to connect
    Then a connection is successfully established

  Scenario: Successfully subscribing to MQTT topics
    Given a connected MQTT client
    When the client subscribes to a topic
    Then the subscription is successful
