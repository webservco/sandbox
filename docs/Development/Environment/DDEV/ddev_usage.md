# DDEV: usage


```sh
# Start
ddev start

# Launch app
ddev launch

# Launch phpMyAdmin
ddev launch -p

# Launch Mailhog
ddev launch -m

# Show details
ddev status

# Connect to MySQL via command line from host
# Find post ("Show details")
mysql --protocol tcp -h 127.0.0.1 -P __PORT__ -u db -p

# Run command in ddev environment
ddev exec __COMMAND__

# Connect to ssh
ddev ssh

# View logs in real time
ddev logs --follow
```
