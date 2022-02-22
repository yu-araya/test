#!/bin/bash

{
  echo "Host server"
  echo "    HostName $1"
  echo "    User $2"
  echo "    IdentityFile ~/.ssh/id_rsa"
  echo "    StrictHostKeyChecking no"
  echo "    UserKnownHostsFile=/dev/null"
} >> ~/.ssh/config