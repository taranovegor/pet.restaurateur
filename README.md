# Restaurateur
Platform for restaurant management automation

## Installation
1. Make sure you have the following dependencies installed on your system:
- docker, docker-compose
2. Copy `.env` and configure environment variables in `.env.local` file
3. Clone the repository:
```bash
git clone https://github.com/taranovegor/pet.restaurateur.git && \
cd pet.restaurateur
```
4. Start the environment:
```bash
make up
```

## Development
Run Composer utility:
```bash
./composer.sh
```
Run Symfony console:
```
./console.sh
```
Run tests:
```bash
make tests-run
```
Run checkstyle:
```bash
make check-style
```
