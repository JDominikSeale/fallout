# Fallout DND/TableTop RPG

This project is based on the game rules/set from the [Fallout: The Roleplaying game](https://marketplace.roll20.net/browse/bundle/16138/fallout-the-roleplaying-game), and is designed to assist the GM/DM(Game Master/Dungeon Master) in tracking details of characters not normally required in games such as "5e D&D".

This is based off 4 players (not including the GM) to be tracked using my system.

As this is based off the [Fallout: The Roleplaying game](https://marketplace.roll20.net/browse/bundle/16138/fallout-the-roleplaying-game), and not a full one to one, __Long Rest__ and __Short Rest__ will only take half from the __Eat/Food__ and __Drink/Water__ statuses.

## Download and setup for offline use

If not already, you may wish to install [XAMPP](https://www.apachefriends.org/) from the XAMPP website and install for your machine.

Once following their guide you may wish to download this repo and put the relevent files in the htdocs folder, I reccomend removing the default file(s) within said folder first.

As you have done this and __running XAMPP__, click __start__ on both __Apache__, and __SQL__ these will start running accordingly.

Select __Admin__ for __SQL__ and __PHP My Admin__ will open in your browser.

Within the __SQL folder__ you will find a __SQL file__ called __Fallout.sql__, which will have everything you need other than __Players__ and __Characters__.

Click the __INSERT__ button found on the top tool bar and __upload__ the __Fallout.sql__ file to SQL, scroll down to the bottom and click go and everythig should upload to the database.

### Custom __INSERT__

__Open__ the new table called __fallout__.

Using the same tool bar at the top select the __SQL__ tab and put in the following.<br/>

Adding players:
Example:<br/>
INSERT INTO characters(name) VALUES("Todd Hardy");
<br/>
Raw:<br/>
INSERT INTO characters(name) VALUES();
<br/>
Do the above __four times for four players__.
<br/>
<br/>
Adding Characters:<br/>
Example:<br/>
INSERT INTO characters(player_id, name) VALUES(5, "Torres");

Raw:<br/>
INSERT INTO characters(player_id, name) VALUES();<br/>
<br/>
Again do the above __four times for four players__, and ensure to add the __player_id__ of your players, this should be as follows.<br/>

|player_id|player name|
|---|---|
|5|"Todd Hardy"|
|6|"Sophie"|
|7|"Kyle"|
|8|"Casandra"|


## Insparation

I made this repo for my own DM, who was, at the time, using a quickly assembled Excel spreadsheet that was janky at best. I, needing a project to build on to improve my SQL and PHP knowledge and skills, surprised him with a prototype and a bombardment of questions, which he quickly answered, as he was as excited as I was for this to start.

I have made other smaller projects in the past using the same technologies, however, having an "end customer" who I could get small amount of feedback from helped with the vision of the project.

### Building the prototype

With already existing code from my other projects I took my database class file and connected it with my new project. 
During building I have been weary of making sure each Class and function have their near singular puprose and made sure to give them appropriate names as to incrrease readability when editing.
Helping to design the actual Database structure was the initial UI design. I identified the key items with the points of seperation and built upon the ideas brought out when designing the websites UI. 

## Learning and Aknowledgments

I understand that many of my aproaches to some of the problems that I have faced may not be best practice, such as calling session_start() on nearly every page, but I aknowledge these and will work on improving on future projects, who knows, this may be fixed in future updates.

By the end to get the build working I seemed to have nearly forgotten the rules I set out at the beginning, and must admit, that at the time of writing this, that the __Long Rest__ function is quite a sloppy mess with many flags being used to handle the act of __Long Rest__. I have learnt from this mistake when making __Short Rest__ and utilised the character class properly to handle __Short Rest. In future I should have the character class also handle __Long Rest__ and not have nested loops.

Another mistake is hard coding the __World__ player and character. I should change this to go off the __Database__ more.

Similarly time to pass should also be held within the __Database__, even though __1 Hour__, __30 Minute__, __10 Minute__, __5 Minute__ should be simple to understand and cover all a DM's needs, adding a __1 Minute__ would require altering the core code base, small, still significant to note. 

AI is a very helpful tool, in spite of this I am proud to state that the back-end and HTML elementent were written with good ol' elbow grease. Sadly I cannot say the same for the CSS, reminder, this project was meant to assist in the building of my back-end skills.

## Login Page
![image](https://github.com/user-attachments/assets/6b4378d3-909b-4ede-9717-95cf011d1c4e)

## DM Management Page
![image](https://github.com/user-attachments/assets/9e0d324b-5c52-42e2-bfe2-f0103853f76f)


