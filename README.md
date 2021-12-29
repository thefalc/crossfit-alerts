# Caunce Types

Source code for the no longer operational website Crossfit Alerts. I created this website back in 2013 as a way for people to follow their favorite athletes during the Crossfit Open and receive email alerts when the athletes update their workout scores.

Details and background about the project are described [here](https://thefalc.com/2013/03/crossfit-open-hacking-oh-my/).

![Crossfit Alerts](/assets/crossfit-alerts.png)

## Technical details

The project runs on the LAMP stack, the backend uses the [CakePHP](http://www.cakephp.org) framework. The frontend is a combination of vanilla Javascript and jQuery. 

The [CrawlersController](https://github.com/thefalc/crossfit-alerts/blob/main/app/Controller/CrawlersController.php) does the heavy lifting to import and crawl athletes. This project was before the Crossfit Games website created a more simplified API. For this project, I had to scrap and parse the actual HTML to convert everything into data that I could use.

**Code structure**
* The backend code serves the player search and data entry. Take a look at the [app/Controller/](https://github.com/thefalc/crossfit-alerts/blob/main/app/Controller/).
* The frontend pages for displaying the search and players can ve viewed in [app/View/](https://github.com/thefalc/crossfit-alerts/tree/main/app/View).
