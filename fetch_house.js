function fetchHouses() {
    fetch('lFetch_houses.php')
        .then(response => response.json())
        .then(data => {
            const houseList = document.getElementById('houseList');
            houseList.innerHTML = ''; // Clear previous list

            data.forEach(house => {
                const houseCard = document.createElement('div');
                houseCard.className = 'house-card';
                houseCard.setAttribute('data-lat', house.lat);
                houseCard.setAttribute('data-lng', house.lng);
                houseCard.innerHTML = `
                    <span class="material-icons house-icon">house</span>
                    <h3>${house.name}</h3>
                    <p>Price: ₱${house.price.toLocaleString()}</p>
                    <p>Location: ${house.location}</p>
                    <button class="view-button" onclick="showHouseDetails('${house.name}', '₱${house.price.toLocaleString()}', '${house.location}', '${house.gender_requirements}')">
                        <span class="material-icons">visibility</span>
                    </button>
                `;
                houseList.appendChild(houseCard);
            });
        })
        .catch(error => console.error('Error fetching houses:', error));
}

// Call fetchHouses when the page loads
window.onload = fetchHouses;