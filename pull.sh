#!/bin/sh
eval $(ssh-agent -s)
ssh-add ~/.ssh/id_rsa.pem
git pull