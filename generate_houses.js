let isGeneratedListVisible = false; // Track if the generated list is visible

function generateHouses(section) {
    // Create an object to hold the data
    const data = {
        genderRequirements: document.getElementById('genderRequirements').value,
        distanceFromUniversity: document.getElementById('distanceFromUniversity').value,
        rentalPrice: document.getElementById('rentalPrice').value,
        accommodationType: document.getElementById('accommodationType').value,
        kitchenType: document.getElementById('kitchenType').value,
        toiletType: document.getElementById('toiletType').value,
        landlordQuarters: document.getElementById('landlordQuarters').value,
        petPolicy: document.getElementById('petPolicy').value,
        visitorPolicy: document.getElementById('visitorPolicy').value,
        noisePolicy: document.getElementById('noisePolicy').value,
        curfewPolicy: document.getElementById('curfewPolicy').value,
        fireExtinguisher: document.getElementById('fireExtinguisher').value,
        surveillanceCamera: document.getElementById('surveillanceCamera').value,
        fenceMaterial: document.getElementById('fenceMaterial').value,
        buildingMaterial: document.getElementById('buildingMaterial').value,
        wifiAvailability: document.getElementById('wifiAvailability').value,
        wifiBilling: document.getElementById('wifiBilling').value,
        electricityIncluded: document.getElementById('electricityIncluded').value,
        electricityBill: document.getElementById('electricityBill').value,
        waterIncluded: document.getElementById('waterIncluded').value,
        waterBill: document.getElementById('waterBill').value
    };

    // Send data to the PHP script
    fetch('generate_houses.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data),
    })
    .then(response => response.json())
    .then(data => {
        const generatedList = document.getElementById('generatedList');
        generatedList.innerHTML = ''; // Clear previous list

        // Loop through the fetched data and create house cards
        data.forEach(house => {
            const houseCard = document.createElement('div');
            houseCard.className = 'generated-house-card';
            houseCard.innerHTML = `
                <h3>${house.name}</h3>
                <p>Price: ₱${house.price.toLocaleString()}</p>
                <p>Location: ${house.location}</p>
                <p>Gender: ${house.gender_requirements}</p>
            `;
            // Add click event to show house details
            houseCard.onclick = function() {
                showHouseDetails(house.name, house.price, house.location, house.gender_requirements);
            };
            generatedList.appendChild(houseCard); // Append the house card to the generated list
        });

        // Show the generated house list container
        document.getElementById('generatedHouseList').style.display = 'block';
        document.getElementById('houseList').style.display = 'none'; // Hide the original house list
        isGeneratedListVisible = true; // Set the flag to indicate the generated list is visible
    })
    .catch(error => {
        console.error('Error fetching houses:', error);
        alert("Failed to fetch houses. Please try again.");
    });
}

function showHouseDetails(title, price, location, gender) {
    // Hide all other sections
    document.querySelector('.house-list').style.display = 'none';
    document.getElementById('map').style.display = 'none';
    document.getElementById('squareButtonContainer').style.display = 'none'; // Hide square buttons
    document.getElementById('settingsSquareButtonContainer').style.display = 'none'; // Hide settings square buttons
    document.getElementById('generalDropdown').style.display = 'none'; // Hide general dropdown
    document.getElementById('policiesDropdown').style.display = 'none'; // Hide policies dropdown
    document.getElementById('securitiesDropdown').style.display = 'none'; // Hide securities dropdown
    document.getElementById('amenitiesDropdown').style.display = 'none'; // Hide amenities dropdown
    document.getElementById('generatedHouseList').style.display = 'none'; // Hide generated house list
    document.getElementById('profileView').style.display = 'none'; // Hide profile view
    document.getElementById('settingsView').style.display = 'none'; // Hide settings view

    // Show the house details section
    document.getElementById('houseDetails').style.display = 'block';
    
    // Set the house details
    document.getElementById('houseName').innerText = title; // Set the house name
    document.getElementById('housePrice').innerText = `Price: ₱${price.toLocaleString()}`; // Set the price
    document.getElementById('houseLocation').innerText = `Location: ${location}`; // Set the location
    
    // Set the gender information
    document.getElementById('houseGender').innerHTML = `Gender: ${gender} <span class="material-icons">${gender === 'Female' ? 'female' : gender === 'Male' ? 'male' : 'people'}</span>`;
}

// Function to hide the house details and return to the generated list
function hideHouseDetails() {
    document.getElementById('houseDetails').style.display = 'none'; // Hide the house details
    if (isGeneratedListVisible) {
        document.getElementById('generatedHouseList').style.display = 'block'; // Show the generated house list again
    } else {
        document.querySelector('.house-list').style.display = 'flex'; // Show the house list (house cards)
    }
}