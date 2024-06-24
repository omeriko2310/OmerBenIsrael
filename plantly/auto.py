import json
import smbus2
from smbus2 import SMBus
from adafruit_seesaw.seesaw import Seesaw
from datetime import datetime
import os
import board

def get_latest_case_id():
    # Get a list of all case IDs in the directory
    case_ids = [int(filename.split('chartData')[1].split('.json')[0]) for filename in os.listdir('/var/www/html/') if filename.startswith('chartData') and filename.endswith('.json') and filename != 'chartData.json'] 
    # Return the maximum case ID
    return max(case_ids)

# Function to read the JSON file
def read_json(filename):
    with open(filename, 'r') as file:
        data = json.load(file)
    return data

# Function to write data to the JSON file
def write_json(filename, data):
    with open(filename, 'w') as file:
        json.dump(data, file, indent=4)

# Function to update data in the JSON file with new sensor readings
def update_data(filename, temp, moisture):
    data = read_json(filename)

    current_time = datetime.now().strftime("%Y-%m-%d | %H:%M")
   
    # Remove the first element from labels and datasets
    data["chart1"]["labels"].pop(7)
    data["chart1"]["labels"] = data["chart1"]["labels"][::-1]
    data["chart1"]["labels"].append(current_time)
    data["chart1"]["labels"] = data["chart1"]["labels"][::-1]
    data["chart1"]["datasets"][0]["data"].pop(7)
    data["chart1"]["datasets"][0]["data"] = data["chart1"]["datasets"][0]["data"][::-1]
    data["chart1"]["datasets"][0]["data"].append(moisture)
    data["chart1"]["datasets"][0]["data"] = data["chart1"]["datasets"][0]["data"][::-1]

    data["chart2"]["labels"].pop(7)
    data["chart2"]["labels"] = data["chart2"]["labels"][::-1]
    data["chart2"]["labels"].append(current_time)
    data["chart2"]["labels"] = data["chart2"]["labels"][::-1]
    data["chart2"]["datasets"][0]["data"].pop(7)
    data["chart2"]["datasets"][0]["data"] = data["chart2"]["datasets"][0]["data"][::-1]
    data["chart2"]["datasets"][0]["data"].append(temp)
    data["chart2"]["datasets"][0]["data"] = data["chart2"]["datasets"][0]["data"][::-1]

    data["chart3"]["labels"].pop(7)
    data["chart3"]["labels"] = data["chart3"]["labels"][::-1]
    data["chart3"]["labels"].append(current_time)
    data["chart3"]["labels"] = data["chart3"]["labels"][::-1]
    data["chart3"]["datasets"][0]["data"].pop(7)
    data["chart3"]["datasets"][0]["data"] = data["chart3"]["datasets"][0]["data"][::-1]
    data["chart3"]["datasets"][0]["data"].append(temp)
    data["chart3"]["datasets"][0]["data"] = data["chart3"]["datasets"][0]["data"][::-1]

    # Write the updated data back to the JSON file
    write_json(filename, data)

latest_case_id = get_latest_case_id()

# Assuming your JSON file is named "chartData.json"
filename = f'/var/www/html/chartData{latest_case_id}.json'

# Your existing code to read sensor data
i2c_bus = board.I2C()
ss = Seesaw(i2c_bus, addr=0x36)
temp = ss.get_temp()
read_list = []
for i in range(100):
	read_list.append(ss.moisture_read())

#moisture = (sum(read_list) / 100)-300
read_list.sort()

# Calculate the median
n = len(read_list)
if n % 2 == 0:
    median = (read_list[n//2 - 1] + read_list[n//2]) / 2
else:
    median = read_list[n/2]

moisture = median - 300
# Update the JSON file with new sensor readings
update_data(filename, temp, moisture)