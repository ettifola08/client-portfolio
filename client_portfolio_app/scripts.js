// scripts.js
document.addEventListener('DOMContentLoaded', () => {
    const loginForm = document.getElementById('loginForm');
    const registerForm = document.getElementById('registerForm');
    const showLoginBtn = document.getElementById('showLogin');
    const showRegisterBtn = document.getElementById('showRegister');

    const loginMessage = document.getElementById('loginMessage');
    const registerMessage = document.getElementById('registerMessage');

    // Function to handle the form switch
    const switchForm = (showForm, hideForm) => {
        showForm.classList.add('active');
        hideForm.classList.remove('active');
    };

    if (showLoginBtn && showRegisterBtn) {
        showLoginBtn.addEventListener('click', (e) => {
            e.preventDefault();
            switchForm(loginForm, registerForm);
        });

        showRegisterBtn.addEventListener('click', (e) => {
            e.preventDefault();
            switchForm(registerForm, loginForm);
        });
    }

    // Check for messages in the URL and display them
    const urlParams = new URLSearchParams(window.location.search);
    const message = urlParams.get('message');
    const status = urlParams.get('status');
    const section = urlParams.get('section');

    if (message && status) {
        if (section === 'login' && loginMessage) {
            loginMessage.textContent = message;
            loginMessage.className = `message ${status}`;
            switchForm(loginForm, registerForm);
        } else if (section === 'register' && registerMessage) {
            registerMessage.textContent = message;
            registerMessage.className = `message ${status}`;
            switchForm(registerForm, loginForm);
        }
    }

    // Dashboard specific scripts
    if (document.body.classList.contains('dashboard-body')) {
        // --- Modal & Form Handlers ---
        const addAssetModal = document.getElementById('addAssetModal');
        const addGoalModal = document.getElementById('addGoalModal');
        const buyAssetModal = document.getElementById('buyAssetModal');
        const sellAssetModal = document.getElementById('sellAssetModal');
        const fundGoalModal = document.getElementById('fundGoalModal');
        const withdrawGoalModal = document.getElementById('withdrawGoalModal');

        const addAssetBtn = document.querySelector('.add-asset-btn');
        const addGoalBtn = document.querySelector('.add-goal-btn');
        const buyBtn = document.querySelector('.buy-btn');
        const closeButtons = document.querySelectorAll('.modal .close-btn');

        // Open Modals
        if (addAssetBtn) addAssetBtn.addEventListener('click', () => addAssetModal.style.display = 'flex');
        if (addGoalBtn) addGoalBtn.addEventListener('click', () => addGoalModal.style.display = 'flex');
        if (buyBtn) buyBtn.addEventListener('click', () => buyAssetModal.style.display = 'flex');

        // Event listener for the "Sell" buttons in the portfolio table
        document.querySelectorAll('.sell-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const assetName = btn.dataset.assetName;
                if (assetName) {
                    document.getElementById('sellAssetName').value = assetName;
                    sellAssetModal.style.display = 'flex';
                } else {
                    alert('Error: Asset name not found for selling.');
                }
            });
        });

        // Event listener for the "Delete" buttons in the portfolio table
        document.querySelectorAll('.delete-asset-btn').forEach(btn => {
            btn.addEventListener('click', async () => {
                const assetName = btn.dataset.assetName;
                if (!assetName) {
                    alert('Error: Asset name not found for deletion.');
                    return;
                }
                
                if (confirm(`Are you sure you want to delete ${assetName} from your portfolio? This action cannot be undone.`)) {
                    try {
                        const response = await fetch('delete_asset.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                            body: `asset_name=${encodeURIComponent(assetName)}`
                        });
                        const result = await response.json();
                        if (result.status === 'success') {
                            alert(result.message);
                            window.location.reload();
                        } else {
                            alert(result.message);
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        alert('An error occurred. Please try again.');
                    }
                }
            });
        });

        document.querySelectorAll('.fund-goal-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const goalId = btn.dataset.goalId;
                document.getElementById('fundGoalId').value = goalId;
                fundGoalModal.style.display = 'flex';
            });
        });

        document.querySelectorAll('.withdraw-goal-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const goalId = btn.dataset.goalId;
                document.getElementById('withdrawGoalId').value = goalId;
                withdrawGoalModal.style.display = 'flex';
            });
        });
        
        document.querySelectorAll('.delete-goal-btn').forEach(btn => {
            btn.addEventListener('click', async () => {
                const goalId = btn.dataset.goalId;
                if (confirm('Are you sure you want to delete this goal? This action cannot be undone.')) {
                    try {
                        const response = await fetch('delete_goal.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                            body: `goal_id=${goalId}`
                        });
                        const result = await response.json();
                        if (result.status === 'success') {
                            alert(result.message);
                            window.location.reload();
                        } else {
                            alert(result.message);
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        alert('An error occurred. Please try again.');
                    }
                }
            });
        });


        // Close Modals
        closeButtons.forEach(btn => {
            btn.addEventListener('click', () => {
                btn.closest('.modal').style.display = 'none';
            });
        });

        window.addEventListener('click', (event) => {
            if (event.target === addAssetModal) addAssetModal.style.display = 'none';
            if (event.target === addGoalModal) addGoalModal.style.display = 'none';
            if (event.target === buyAssetModal) buyAssetModal.style.display = 'none';
            if (event.target === sellAssetModal) sellAssetModal.style.display = 'none';
            if (event.target === fundGoalModal) fundGoalModal.style.display = 'none';
            if (event.target === withdrawGoalModal) withdrawGoalModal.style.display = 'none';
        });

        // --- AJAX Form Submissions ---
        const handleFormSubmission = async (formId, endpoint, modalId) => {
            const form = document.getElementById(formId);
            const modal = document.getElementById(modalId);
            if (!form) return;

            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                const formData = new FormData(form);

                try {
                    const response = await fetch(endpoint, {
                        method: 'POST',
                        body: formData
                    });
                    
                    if (!response.ok) {
                        throw new Error(`HTTP error! Status: ${response.status}`);
                    }
                    
                    const result = await response.json();
                    
                    if (result.status === 'success') {
                        alert(result.message);
                        if (modal) modal.style.display = 'none';
                        window.location.reload();
                    } else {
                        alert(result.message);
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('An error occurred while submitting. Please check your server connection and try again.');
                }
            });
        };

        handleFormSubmission('addAssetForm', 'add_asset.php', 'addAssetModal');
        handleFormSubmission('addGoalForm', 'add_goal.php', 'addGoalModal');
        handleFormSubmission('buyAssetForm', 'update_asset.php', 'buyAssetModal');
        handleFormSubmission('sellAssetForm', 'update_asset.php', 'sellAssetModal');
        handleFormSubmission('fundGoalForm', 'fund_goal.php', 'fundGoalModal');
        handleFormSubmission('withdrawGoalForm', 'withdraw_goal.php', 'withdrawGoalModal');


        // --- Interest Calculator Logic ---
        const calculatorForm = document.getElementById('interestCalculatorForm');
        const calculatorResult = document.getElementById('calculatorResult');
        if (calculatorForm) {
            calculatorForm.addEventListener('submit', (e) => {
                e.preventDefault();
                const principal = parseFloat(document.getElementById('principal').value);
                const rate = parseFloat(document.getElementById('rate').value) / 100;
                const years = parseFloat(document.getElementById('years').value);
                const frequency = parseInt(document.getElementById('frequency').value);

                if (isNaN(principal) || isNaN(rate) || isNaN(years) || isNaN(frequency) || principal <= 0 || years <= 0) {
                    calculatorResult.innerHTML = '<p class="error">Please enter valid positive numbers for all fields.</p>';
                    calculatorResult.classList.add('show');
                    return;
                }

                const futureValue = principal * Math.pow((1 + (rate / frequency)), (frequency * years));
                const totalInterest = futureValue - principal;

                document.getElementById('futureValue').textContent = `₦${futureValue.toFixed(2)}`;
                document.getElementById('totalInterest').textContent = `₦${totalInterest.toFixed(2)}`;
                calculatorResult.classList.add('show');
            });
        }

        // --- Performance Chart Logic ---
        const ctx = document.getElementById('performanceChart');
        if (ctx) {
            // Placeholder for fetching dynamic data. For now, we'll use mock data.
            const chartLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'];
            const chartData = [10000, 10500, 11200, 11000, 11500, 12000]; // Mock Naira values

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: chartLabels,
                    datasets: [{
                        label: 'Portfolio Value (₦)',
                        data: chartData,
                        borderColor: '#1976d2',
                        backgroundColor: 'rgba(25, 118, 210, 0.2)',
                        borderWidth: 2,
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                        }
                    },
                    scales: {
                        x: {
                            display: true,
                            title: {
                                display: true,
                                text: 'Month'
                            }
                        },
                        y: {
                            display: true,
                            title: {
                                display: true,
                                text: 'Value (₦)'
                            }
                        }
                    }
                }
            });
        }
    }
});
