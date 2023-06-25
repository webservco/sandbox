# Xdebug troubleshooting

Check logs: `ddev logs -f`

Make sure Xdebug can connect to the correct IP: `telnet __IP__ 9003`.

Note: seems while `127.0.0.1` works with telnet, it can't be sued by Xdebug and we need the external/host IP;
