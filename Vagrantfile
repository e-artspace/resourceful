# -*- mode: ruby -*-
# vi: set ft=ruby :

$script = <<-SCRIPT
	apt-get update
	
	# Install php aand composer
	apt-get -y install git curl php7.0-cli php7.0-common php7.0-mbstring php7.0-bz2 php7.0-zip php7.0-xml php7.0-curl
	curl -sS https://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin --filename=composer
	cd /vagrant; composer install
	
	# install apache
	apt-get -y install apache2 libapache2-mod-php7.0 redis-server
	cat <<EOF > /etc/apache2/sites-available/000-default.conf
<VirtualHost *:80>
    DocumentRoot /vagrant/example/web
    RewriteEngine On		
    <Directory /vagrant/example/web >
        AllowOverride None
        Require all granted
        RewriteCond %{REQUEST_FILENAME} !-d
        RewriteCond %{REQUEST_FILENAME} !-f
        RewriteRule ^(.*)$ index.php [QSA,L]
     </Directory>
</VirtualHost>
EOF
	a2enmod rewrite
	service apache2 restart
	
	# turn on assertion engine and display errors
	sed -i "s/zend.assertions =.*/zend.assertions = 1/" /etc/php/7.0/cli/php.ini
	sed -i "s/^display_errors = Off/display_errors = On/" /etc/php/7.0/cli/php.ini
	
	echo "======================================================================================"
	echo "Point your browser to:"
	echo "http://json-browser.s3-website-us-west-1.amazonaws.com/?url=http%3A//localhost%3A8080/"
	echo "======================================================================================"
	
SCRIPT


VAGRANTFILE_API_VERSION = '2'
Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|
	config.vm.box = "bento/ubuntu-16.04"  # Same of the one used by opcode chef kitchen.
	config.vm.provision "shell", inline: $script
	config.vm.network "forwarded_port", guest: 80, host: 8080
	config.vm.provider "virtualbox" do |v|
	  v.memory = 1536
	end
end