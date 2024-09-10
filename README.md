# Diceus (Insider) test task

## Requirements

- Docker

## Setup

1. Add `::1	diceus-sim.local api.diceus-sim.local` to Your `/etc/hosts` file to be able to access an API and SPA
   application
2. Run `cd docker && docker-compose -f docker-compose.local.yml` to spin up a project. You might want to add `up -d` to
   be able to run in a detached mode. `-p` to specify a project/stack name
3. Run `php artisan migrate:fresh --seed; php artisan queue:listen --queue=league-game` from a `diceus-sim-api`
   container to seed the data and start the queue for the league games
4. Open `api.diceus-sim.local` and accept local certificates
5. Open `diceus-sim.local` and try using

## What and How could it be done differently?

As You can see - I am not a pro at front-end, but I am trying to do my best to achieve needed result.
Now, what could be done else and/or differently?

- I could use Laravel Reverb / Mercury.io for a Real-time notification/data update system
- Caching technique in a real project will be different (tags, different sources (redis, database, etc.)) and will
  depend on a data severity/importance level and how often should it be called/updated
- Different Vue components placing / framework / Vue version in general

## What I haven't done

- I haven't done "Play All" functionality like it should be. I did it in the way so it queue all week games to be
  played, but all league. That makes much more sense.
- I haven't done any Unit/Feature testing for this, because time consumption for this would be not acceptable at the
  moment. If You want me to share testing experience - You can query it from me
- Front data edition. If You want to edit data - You can go to the database (SQLite) and edit needed values on the
  `league_team` table on the statistics field
- Haven't used MySQL. It would be an additional time consumption; for the sake of MVP/PoC - SQLite would be more than enough

If You would have any questions - please, don't hesitate to pass them.
