#!/bin/bash

echo hello
cd ..
rm -f GoogleAnalyticsPluginForRevive.zip
zip -r GoogleAnalyticsPluginForRevive.zip plugins -x "*.DS_Store"