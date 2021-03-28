# TextFormater in PHP
This is a formatting library for the specific styling and formats of the Source Texts available from Urban Monastics.

The source texts use a subset of the Markdown functionality, and include some additional features. To make these texts more approachable and useful to others we wanted to publish a library which makes formatting them simple and direct. This library is based upon [Parsedown](https://github.com/erusev/parsedown) by [Emanuil Rusev](http://erusev.com). 

## Features  

*	One File without any Dependencies
*	Fast
*	


## Adding to your Project  
  
Install the composer package:  
  
	composer require UrbanMonastics/TextFormater-PHP
  
Or download the latest release and include `TextFormater.php`  
  
## Example Usage  
In the most simple approach you can pass text to be parsed.  

	$TextFormater = new TextFormater();
	
	echo $TextFormater->text("Hello *TextFormater*!");  # prints: <p>Hello <em>TextFormater</em>!</p>

You can also take advantage of the structure of the source texts.


## Formatting Options
These texts may need to be used in various formats and contexts. There are going to be situations in which you may want to ensure that only certain elements of text are rendered for your use case.



	$TextFormater->setLiturgicalElements( true );
	echo $TextFormater->text("God, [+] come to my assistance,[*]");  # prints: <p>God, <span class="symbol-cross">✛</span> come to my assistance,<span class="symbol-star">*</span></p>

	$TextFormater->setLiturgicalHTML( false );	# The default value is True, so you can manualy disable wrapping liturgical elements.
	echo $TextFormater->text("God, [+] come to my assistance,[*]");  # prints: <p>God, ✛ come to my assistance,*</p>


## Development Environment
To make it easier to develop and build out the TextFormater we have setup a local docker container for you to use. There are some simple unix scripts from the project base directory that you can execute to get setup.

	# To build or update the container
	./docker/build.sh
	
	# To start an existing container
	./docker/start.sh
	
	# To stop/shutdown the container
	./docker/stop.sh
	
	# To attach to the running container
	./docker/attach.sh
	
	# To run the PHP composer update on the running container
	./docker/update.sh

In addition we have linked the NGNIX access and error logs to files in the docker directory. This can prove helpful when trouble shooting.

	docker/nginx/access.log
	docker/nginx/error.log