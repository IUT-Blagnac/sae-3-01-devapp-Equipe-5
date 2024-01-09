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

  Scenario: Successfully receiving and processing MQTT messages
    Given a connected MQTT client subscribed to topics
    When a message is published to a subscribed topic
    Then the message is received and processed correctly

  Scenario: Test printing the average
    Given a datatest.csv
    When the average method is call
    Then the method sould print good results
