
# Library.md - project, knowledge for all.

## Features

Library.md is a trackerless knowledge sharing site.
It's integrated with dht-client-daemon to fetch the number of peers from DHT.

## Stack

Mysql, php 5.6.
Sphinx for search.
Mongodb for torrent tags.
dht-client-daemon for dht peer retrieval.

## Auto-installer

Auto-installer is based on ansible.
It was tested on Ubuntu 16.x and 17.x.
A user the name "ubuntu" with must exist, some cron jobs will be added to it.
You need to run one command to install and configure everything.
Run it as root or a sudo enabled user.

```
./install-all.sh
```

## Local development with vagrant

If you want to test locally this version, you can install the app in a VM using virtualbox & vagrant.

Install [vagrant](https://www.vagrantup.com/docs/installation/) and [Virtualbox](https://www.virtualbox.org/wiki/Downloads).
Familiarize yourself with ansible/Vagrantfile to see the ports that are redirected.

```
cd ansible
vagrant up
ssh-add ansible/.vagrant/machines/default/virtualbox/private_key
ansible-playbook -i ansible/hosts ansible/play_main.yml
```
