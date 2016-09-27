# -*- mode: ruby -*-
# vi: set ft=ruby :

$script = <<-SCRIPT
	apt-get update
	apt-get -y install git curl php7.0-cli php7.0-common php7.0-mbstring php7.0-bz2 php7.0-zip php7.0-xml php7.0-curl
	curl -sS https://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin --filename=composer
	
	# turn on assertion engine
	sed -i "s/zend.assertions =.*/zend.assertions = 1/" /etc/php/7.0/cli/php.ini
SCRIPT


VAGRANTFILE_API_VERSION = '2'
Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|
	config.vm.box = "bento/ubuntu-16.04"  # Same of the one used by opcode chef kitchen.
	config.vm.provision "shell", inline: $script
	config.vm.provider "virtualbox" do |v|
	  v.memory = 1536
	end
end