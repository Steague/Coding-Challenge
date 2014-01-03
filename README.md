Warm Up:
1. Given a directory, retrieve a list of all files within directory and sub-directory (iterative or recursive).
2. Given an XML document with the following schema, please produce an order list of the steps as a string:
 
<root>
<instructions>
<step order="1">Cook spaghetti</step>
<step order="3">Add Sauce</step>
<step order="2">Drain from pot</step>
</instructions>
<dish>Pasta</dish>
</root>
 
Task 1
Create a web application in php that displays the maximum, minimum, and average temperature for San Francisco airport for a particular day.  The data can be retrieved from the National Climate Date Center website (http://www.ncdc.noaa.gov/most-popular-data#lcdus > Quality Controlled Local Climatological Data > California > SFO.  The application should provide the following functionality:
 
1. Data should be downloaded from the climate date center and imported into a mysql database
2. Create a view that displays the temperature results in a table
3. Users should also be able to click a refresh button on the page that will re-fresh the temperature data via an ajax call.
 
Bonus Points: Provide a simple API to manage the data you have ingested in accordance with a rest / resource oriented architecture.
 
Task 2
Create an application (either web based or cli) that accepts two arguments for input.  The system should output the sum of the two numbers but without using the native addition or subtraction operator within PHP.
 
Task 3
For the following function please provide both a recursive and iterative solution for any given input:
 
f(x) = f(x-1)^3 + f(x-2)^2 + f(x-3)
 
where
 
f(3) = 3
f(2) = 2
f(1) = 1
 
Essentially translate the mathematical notation into PHP code.