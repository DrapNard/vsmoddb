#!/bin/bash

# Colors for output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo "${YELLOW}Starting VSModDB services...${NC}"

# Check if docker-compose is available
if ! command -v docker-compose &> /dev/null; then
    echo "Error: docker-compose is not installed. Please install Docker Compose first."
    exit 1
fi

# Start all services
echo "${YELLOW}Starting Docker containers...${NC}"
docker-compose down --remove-orphans
docker-compose up -d

# Function to check if a service is ready
wait_for_service() {
    local service=$1
    local port=$2
    local max_attempts=30
    local attempt=1

    echo "${YELLOW}Waiting for ${service} to be ready...${NC}"
    while ! nc -z localhost $port && [ $attempt -le $max_attempts ]; do
        printf "."
        sleep 2
        attempt=$((attempt + 1))
    done
    echo ""

    if [ $attempt -le $max_attempts ]; then
        echo "${GREEN}✓ ${service} is ready${NC}"
        return 0
    else
        echo "Error: ${service} did not become ready in time"
        return 1
    fi
}

# Wait for services to be ready
wait_for_service "Frontend" 3000
wait_for_service "Backend" 80
wait_for_service "Database" 3306
wait_for_service "Adminer" 8080

# If all services are ready, show access information
if [ $? -eq 0 ]; then
    echo "\n${GREEN}All services are running!${NC}"
    echo "\nAccess your services at:"
    echo "${GREEN}Frontend:${NC} http://localhost:3000"
    echo "${GREEN}Backend:${NC} http://localhost:80"
    echo "${GREEN}Adminer:${NC} http://localhost:8080"
    echo "${GREEN}Database:${NC} localhost:3306"
    echo "\nDatabase credentials:"
    echo "Database: moddb"
    echo "Username: vsmoddb"
    echo "Password: vsmoddb"
    echo "\n${YELLOW}Press Ctrl+C to stop all services${NC}"

    # Keep the script running to show logs
    docker-compose logs -f
else
    echo "\n${YELLOW}Some services failed to start. Please check the logs above.${NC}"
    docker-compose logs
    exit 1
fi