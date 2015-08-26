#!/bin/bash
# Automatically generated script by
# vagrantbox/doc/src/vagrant/src-vagrant/deb2sh.py
# The script is based on packages listed in debpkg_minimal.txt.

#set -x  # make sure each command is printed in the terminal

function apt_install {
  sudo apt-get -y install $1
  if [ $? -ne 0 ]; then
    echo "could not install $1 - abort"
    exit 1
  fi
}

function pip_install {
  sudo pip install "$@"
  if [ $? -ne 0 ]; then
    echo "could not install $p - abort"
    exit 1
  fi
}

sudo apt-get update --fix-missing

# Minimal installation for a Python ecosystem
# for Bluetooth
echo "Adding www-data user to Bluetooth Group"
sudo usermod -a -G bluetooth www-data

# Dpkg
echo "Installation des dependances"
apt_install expect
apt_install bluez
apt_install python-gobject
apt_install python-pip
apt_install python-dbus
apt_install bluez-tools
#apt_install bluez-utils
#apt_install bluez-alsa
pip_install pybluez

#sudo wget http://www.kernel.org/pub/linux/bluetooth/bluez-5.30.tar.xz
#sudo dpkg --get-selections | grep -v deinstall | grep bluez
#sudo tar xvf bluez-5.30.tar.xz
#sudo apt-get install libglib2.0-dev libdbus-1-dev libusb-dev libudev-dev libical-dev systemd libreadline-dev
#sudo ./configure --enable-library
#sudo make -j8 && sudo make install 
#sudo cp attrib/gatttool /usr/local/bin/

echo "Everything is successfully installed!"

sudo /etc/init.d/bluetooth restart
sudo /etc/init.d/nginx restart