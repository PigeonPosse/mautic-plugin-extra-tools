<!--

██████╗ ██╗ ██████╗ ███████╗ ██████╗ ███╗   ██╗
██╔══██╗██║██╔════╝ ██╔════╝██╔═══██╗████╗  ██║
██████╔╝██║██║  ███╗█████╗  ██║   ██║██╔██╗ ██║
██╔═══╝ ██║██║   ██║██╔══╝  ██║   ██║██║╚██╗██║
██║     ██║╚██████╔╝███████╗╚██████╔╝██║ ╚████║
╚═╝     ╚═╝ ╚═════╝ ╚══════╝ ╚═════╝ ╚═╝  ╚═══╝
                                               
██████╗  ██████╗ ███████╗███████╗███████╗      
██╔══██╗██╔═══██╗██╔════╝██╔════╝██╔════╝      
██████╔╝██║   ██║███████╗███████╗█████╗        
██╔═══╝ ██║   ██║╚════██║╚════██║██╔══╝        
██║     ╚██████╔╝███████║███████║███████╗      
╚═╝      ╚═════╝ ╚══════╝╚══════╝╚══════╝      
                                               
███╗   ███╗ █████╗ ██╗   ██╗████████╗██╗ ██████╗    
████╗ ████║██╔══██╗██║   ██║╚══██╔══╝██║██╔════╝    
██╔████╔██║███████║██║   ██║   ██║   ██║██║         
██║╚██╔╝██║██╔══██║██║   ██║   ██║   ██║██║         
██║ ╚═╝ ██║██║  ██║╚██████╔╝   ██║   ██║╚██████╗    
╚═╝     ╚═╝╚═╝  ╚═╝ ╚═════╝    ╚═╝   ╚═╝ ╚═════╝    
                                                    
███████╗██╗  ██╗████████╗██████╗  █████╗            
██╔════╝╚██╗██╔╝╚══██╔══╝██╔══██╗██╔══██╗           
█████╗   ╚███╔╝    ██║   ██████╔╝███████║           
██╔══╝   ██╔██╗    ██║   ██╔══██╗██╔══██║           
███████╗██╔╝ ██╗   ██║   ██║  ██║██║  ██║           
╚══════╝╚═╝  ╚═╝   ╚═╝   ╚═╝  ╚═╝╚═╝  ╚═╝           
                                                    
████████╗ ██████╗  ██████╗ ██╗     ███████╗         
╚══██╔══╝██╔═══██╗██╔═══██╗██║     ██╔════╝         
   ██║   ██║   ██║██║   ██║██║     ███████╗         
   ██║   ██║   ██║██║   ██║██║     ╚════██║         
   ██║   ╚██████╔╝╚██████╔╝███████╗███████║         
   ╚═╝    ╚═════╝  ╚═════╝ ╚══════╝╚══════╝   
                                                                     

CREATED BY 	ANGELO <angelespejo13@gmail.com>
FOR 		PIGEONPOSSE.COM

-->

# Mautic Extra Tools by PIGEONPOSSE™

[![Web](https://img.shields.io/badge/Web-grey?style=flat-square)](https://pigeonposse.com/) 
[![About us](https://img.shields.io/badge/About--us-grey?style=flat-square)](https://pigeonposse.com/?popup=about) 
[![Donate ko-fi](https://img.shields.io/badge/Donate-pink?style=flat-square)](https://pigeonposse.com/?popup=donate) 

## 🗒 Description

This plugin adds various functionality to _Mautic_ and is inspired by others that the mautic community has developed. To learn more about the history of the project, see the [history section](https://github.com/PigeonPosse/mautic-plugin-extra-tools/blob/main/HISTORY.md).

### What does this plugin offer?
- Creation of **custom tokens** that work on both pages, emails and notifications
- Execution of **mautic commands from the interface** without having to open the terminal (still in development)
- Creation of **custom navigation links** for the main, user, administrator and extra menus

The idea of this plugin is to bring together some of the features that the community needs and that Mautic does not offer natively. So any ideas are welcome.

## 🔑 Installation

### Manual
1. Download or clone this bundle into your _Mautic_ <code>/plugins</code> folder.
2. Clean your _Mautic_ cache. There are two options to do that:
	- Remove cache from folder "prod":
		```bash 
		# For Mautic 3.x: 
		rm -r {YourMauticDirectory}/var/cache/prod 
		# For Mautic 2.x:
		rm -r {YourMauticDirectory}/app/cache/prod
		```
	- Execute Mautic cache command: 
		```bash
		# For Mautic 3.x: 
		php {YourMauticDirectory}/bin/console cache:clear
		# For Mautic 2.x:
		php {YourMauticDirectory}/app/console cache:clear
		```
3. In the Mautic GUI, go to the ⚙️ icon in the top right and then to Plugins.
4. Click the _"Install/Upgrade Plugins"_ button in the top right. 

	>**Note:** If you are on an older version of Mautic, click the drowpdown arrow in the top right and then choose _"Install/Upgrade Plugins"_.

5. Finally, You should now see _PigeonPosse_ plugin in your list of plugins.

## 📝 History

Read about the history of the project

[Read more](https://github.com/PigeonPosse/mautic-plugin-extra-tools/blob/main/HISTORY.md)

## 👨‍💻 Development

Info about mautic development
- https://developer.mautic.org/

Info about symfony
- https://symfony.com/doc

## ☕ Donate

Help us to develop more interesting things

[Donate](https://pigeonposse.com/?popup=donate) 

## 📜 License

This sofware is licensed with GPLv3 (GNU GENERAL PUBLIC LICENSE Version 3)

[Read more](https://github.com/PigeonPosse/mautic-plugin-extra-tools/blob/main/LICENSE)

## 🐦 About us

PigeonPosse is a ✨ code development collective ✨ focused on creating practical and interesting tools that help developers and users enjoy a more agile and comfortable experience. Our projects cover various programming sectors and we do not have a thematic limitation in terms of projects.

### Collaborators

|                                                                                    | Name        | Role         | GitHub                                         |
| ---------------------------------------------------------------------------------- | ----------- | ------------ | ---------------------------------------------- |
| <img src="https://github.com/AngelEspejo.png?size=72" style="border-radius:100%"/> | AngelEspejo | Author       | [@AngelEspejo](https://github.com/AngelEspejo) |
| <img src="https://github.com/PigeonPosse.png?size=72" style="border-radius:100%"/> | PigeonPosse | Collective	  | [@PigeonPosse](https://github.com/PigeonPosse) |


<br>

