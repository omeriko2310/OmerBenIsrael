import subprocess
import sys


activate_script = '/home/mentos/env/bin/activate'
activation_cmd = f'/home/mentos/env/bin/python3 "/var/www/html/auto.py"'
subprocess.call(activation_cmd, shell=True)
