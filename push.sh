#!/bin/sh
eval $(ssh-agent -s)
ssh-add ~/.ssh/id_rsa
git add .
git commit -m "last changes"
git push