# llm-ethical-benchmarker

this project helps you test and compare how different language models handle ethical scenarios. you can create scenarios, set up different personas, and see how each model responds. the app also checks for bias and fairness in the answers.

## how to run it

1. **requirements:**  
   - docker and docker compose installed

2. **setup:**  
   - copy `.env.example` to `.env` and fill in your settings (mainly database stuff)
   - run `docker compose up --build` to start everything

3. **migrate the database:**  
   - in another terminal, run:  
     `docker exec laravel-app php artisan migrate`

4. **visit the app:**  
   - open [http://localhost](http://localhost) in your browser

## running tests

to run the tests, use:  
`docker exec laravel-app php artisan test`

## background jobs

if you want to process queued jobs (like bias scoring), run:  
`docker exec laravel-app php artisan queue:work`

## github actions

this repo has a workflow that runs your tests and checks code style on every push or pull request to `main`.  
make sure you set these secrets in your repo settings:
- `DB_DATABASE`
- `DB_USERNAME`
- `DB_PASSWORD`

## questions?

if you get stuck, check your docker containers are running and your `.env` matches your docker-compose settings.
