
Warning: Bear in mind that this is an on going project and that everything might no be coded as expected. But hey, we are free to improve it. 

# Lumen Assorted micro services


Even though I'm most familiar with Yii2 I've decided to give a try at Laravel through Lumen.
During developments I'm often confronted to common tasks, therefore I though I could create a Rest Api layer common to my projects. In other words, a collection of microservices I can use in any other development. 

As most of my backends are using PHP (Directus, wordpress, Yii2) I picked Lumen (thou Tii2, Directus and WP are also able to perform that). 

But I wanted to try Lumen instead of Yii2 with which I've been developing the most. 

## What is it ?
It is a simple Lumen installation with added controllers and routes, so...no big deal here.

## Installation
Clone this repo and run composer update

## Endpoints

You could easily get it from the */routes/web.php* file. All routes are explained there.

Anyway..

This endpoint will return the page meta data from an URL

example: `https://yourApiSite/v1/url/fetchmeta?url=https://fifteenpeas.com`
  

#### This endpoint will check if vat number is valid or not and will return data for valid number
example: `https://yourApiSite/v1/vat/checkvat?vat=BE25869157`

*This uses the EU checkvat service.*


#### This endpoint will return country code based on the ip address of the requestor

example: `https://yourApiSite/v1/vat/locateip`

will return something like this `1;SE;SWE;Sweden`

  *This uses the [https://ip2c.org/](https://ip2c.org/%27) service.*

#### This endpoint will return all available vatRates available

example : `https://yourApiSite/v1/vat/vatrates`

This is done thanks to a github project worth checking which inspired me a lot [https://github.com/ibericode/vat](https://github.com/ibericode/vat)

#### This endpoint will return the vat rate of the located ip country address

example: `https://yourApiSite/v1/vat/ipvatrate`
  
Unless you have a vpn, this will give your country list of vat rates.  

## Using Google and Mailgun

The following endpoint expects an api key from google


The folloing endpoint expects an api key and domain from Mailgun. The whole idea is
to be able to receive an email for any which site that uses, for example, a contact form. In order to identify the website there is a required source parameter. Also, insted of using GET we will be using POST.



# A VUEJS APP
In oder to be complete on how to use this microservices project there will be a VUEJS, independant, web app. 

## Contributing

I will certainly add more endpoints based on my needs, however,
You are free to submit anything you think will fit. I'll check the pull requests.
  
