# llm ethical benchmarker

this is a web app for testing and comparing how different large language models (llms) handle ethical scenarios. you can use it to see how models like gpt-4, claude, and others respond to tough questions, and check their answers for bias or fairness.

## what you can do

- create and edit ethical scenarios and personas (roles)
- run tests to see how different llms answer each scenario
- see scores for bias, stereotypes, and fairness for each response
- compare model answers side by side
- add your own human answers for comparison
- see summaries (tldr) for each response

## how it works

1. pick or create a scenario and a persona (like a judge, regulator, etc)
2. choose a model to run (gpt-4, claude, etc)
3. the app sends the prompt to the model and saves the answer
4. the answer is automatically scored for bias and stereotypes
5. you can view all results, scores, and summaries in the web ui

## setup

- clone the repo and install dependencies:
  ```bash
  composer install
  npm install
  ```
- copy `.env.example` to `.env` and set your openrouter api key and database settings
- run migrations:
  ```bash
  php artisan migrate
  ```
- (optional) seed the database with example data:
  ```bash
  php artisan db:seed
  ```
- start the dev servers:
  ```bash
  php artisan serve
  npm run dev
  ```
- to process model requests and scoring, run the queue worker:
  ```bash
  php artisan queue:work --queue=llm,scoring
  ```

## notes
- you need an openrouter api key to use real llm models
- the app uses sqlite by default, but you can use mysql or postgres
- everything runs locally by default

## license

this project is open source under the mit license.
