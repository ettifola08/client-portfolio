<?php
// dashboard.php
// The main client dashboard for TrustBanc.
//
// This page is a protected route. If a user is not logged in, they are redirected to the login page.
// It dynamically fetches and displays the user's portfolio, financial goals, and provides other tools.

session_start();

// Check if client is logged in. If not, redirect to the login page.
if (!isset($_SESSION['user_id'])) {
    header("Location: index.html");
    exit();
}

include 'db_connect.php';

$userId = $_SESSION['user_id'];
$fullName = $_SESSION['full_name'];
$firstName = explode(' ', trim($fullName))[0];

// Fetch client's risk profile
$riskProfile = 'N/A';
$stmt = $conn->prepare("SELECT risk_profile, cash_balance FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$cashBalance = 0; // Initialize cash balance
if ($row = $result->fetch_assoc()) {
    $riskProfile = $row['risk_profile'];
    $cashBalance = $row['cash_balance'];
}
$stmt->close();

// Fetch portfolio data for the user
$portfolio = [];
$totalPortfolioValue = 0;
$totalInvested = 0;
$stmt = $conn->prepare("SELECT id, asset_name, quantity, purchase_price, current_price FROM portfolios WHERE user_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $currentValue = $row['quantity'] * $row['current_price'];
    $investedValue = $row['quantity'] * $row['purchase_price'];
    $row['current_value'] = $currentValue;
    $row['profit_loss'] = $currentValue - $investedValue;
    $row['profit_loss_percent'] = $investedValue > 0 ? (($currentValue - $investedValue) / $investedValue) * 100 : 0;
    $portfolio[] = $row;
    $totalPortfolioValue += $currentValue;
    $totalInvested += $investedValue;
}
$stmt->close();

// Include cash balance in total portfolio value
$totalPortfolioValue += $cashBalance;

// Calculate overall performance
$overallProfitLoss = $totalPortfolioValue - $totalInvested;
$overallReturn = $totalInvested > 0 ? ($overallProfitLoss / $totalInvested) * 100 : 0;

// Fetch financial goals for the user
$goals = [];
$stmt = $conn->prepare("SELECT id, goal_name, target_amount, saved_amount, end_date FROM financial_goals WHERE user_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $goals[] = $row;
}
$stmt->close();

// Close the database connection
$conn->close();

// Expanded list of Nigerian stocks for the dropdown menu
$nigerianStocks = [
    "Dangote Cement", "MTN Nigeria", "Zenith Bank", "Access Bank", "Guaranty Trust Holding Company",
    "FBN Holdings", "Bua Cement", "Nigerian Breweries", "Nestle Nigeria", "Seplat Energy",
    "United Bank for Africa", "Okomu Oil", "Presco Plc", "Stanbic IBTC Holdings", "First City Monument Bank",
    "Flour Mills of Nigeria", "Transcorp Plc", "Fidelity Bank", "Wema Bank", "Ecobank Transnational",
    "Lafarge Africa", "Guinness Nigeria", "Cadbury Nigeria", "PZ Cussons", "Mobil Oil Nigeria",
    "Total Nigeria", "Conoil Plc", "Eterna Plc", "MRS Oil Nigeria", "Forte Oil",
    "UACN Plc", "Union Bank of Nigeria", "Sterling Bank", "Unity Bank", "Jaiz Bank",
    "May and Baker Nigeria", "GlaxoSmithKline", "Nigerian Aviation Handling Company", "International Breweries",
    "Dangote Sugar Refinery", "BUA Foods", "Custodian Investment", "Cornerstone Insurance", "AIICO Insurance",
    "Sovereign Trust Insurance", "Royal Exchange", "Prestige Assurance", "Mutual Benefits Assurance",
    "LASACO Assurance", "Linkage Assurance"
];
sort($nigerianStocks); // Sort the stocks alphabetically for easy access
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TrustBanc - Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="dashboard-body">
    <header class="dashboard-header">
        <div class="header-left">
            <h1 class="logo"><i class="fas fa-chart-area"></i> TrustBanc</h1>
        </div>
        <div class="header-right">
            <span>Welcome, <?php echo htmlspecialchars($firstName); ?>!</span>
            <a href="logout.php" class="logout-btn">Logout</a>
        </div>
    </header>

    <main class="dashboard-container">
        <div class="summary-cards">
            <div class="summary-card">
                <h3>Total Portfolio Value</h3>
                <p class="value">₦<?php echo htmlspecialchars(number_format($totalPortfolioValue, 2)); ?></p>
            </div>
            <div class="summary-card">
                <h3>Total Profit/Loss</h3>
                <p class="value <?php echo ($overallProfitLoss >= 0) ? 'positive' : 'negative'; ?>">
                    ₦<?php echo htmlspecialchars(number_format($overallProfitLoss, 2)); ?>
                </p>
            </div>
            <div class="summary-card">
                <h3>Overall Return</h3>
                <p class="value <?php echo ($overallReturn >= 0) ? 'positive' : 'negative'; ?>">
                    <?php echo htmlspecialchars(number_format($overallReturn, 2)); ?>%
                </p>
            </div>
            <div class="summary-card">
                <h3>Risk Profile</h3>
                <p class="value risk-profile"><?php echo htmlspecialchars($riskProfile); ?></p>
            </div>
        </div>

        <div class="grid-container">
            <!-- Portfolio Tracking Section -->
            <div class="card portfolio-section">
                <h2><i class="fas fa-briefcase"></i> My Portfolio</h2>
                <div class="table-container">
                    <?php if (count($portfolio) > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Asset</th>
                                <th>Quantity</th>
                                <th>Purchase Price</th>
                                <th>Current Price</th>
                                <th>Current Value</th>
                                <th>P&L (₦)</th>
                                <th>P&L (%)</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($portfolio as $asset): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($asset['asset_name']); ?></td>
                                <td><?php echo htmlspecialchars(number_format($asset['quantity'], 4)); ?></td>
                                <td>₦<?php echo htmlspecialchars(number_format($asset['purchase_price'], 2)); ?></td>
                                <td>₦<?php echo htmlspecialchars(number_format($asset['current_price'], 2)); ?></td>
                                <td>₦<?php echo htmlspecialchars(number_format($asset['current_value'], 2)); ?></td>
                                <td class="<?php echo ($asset['profit_loss'] >= 0) ? 'positive' : 'negative'; ?>">₦<?php echo htmlspecialchars(number_format($asset['profit_loss'], 2)); ?></td>
                                <td class="<?php echo ($asset['profit_loss_percent'] >= 0) ? 'positive' : 'negative'; ?>"><?php echo htmlspecialchars(number_format($asset['profit_loss_percent'], 2)); ?>%</td>
                                <td>
                                    <button class="action-btn sell-btn" data-asset-name="<?php echo htmlspecialchars($asset['asset_name']); ?>">Sell</button>
                                    <button class="action-btn delete-asset-btn" data-asset-name="<?php echo htmlspecialchars($asset['asset_name']); ?>">Delete</button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php else: ?>
                    <p class="no-data-message">Your portfolio is empty. Start investing today!</p>
                    <?php endif; ?>
                </div>
                <div class="action-buttons">
                    <button class="btn add-asset-btn"><i class="fas fa-plus-circle"></i> Add Asset</button>
                    <button class="btn buy-btn"><i class="fas fa-money-bill-wave"></i> Buy</button>
                </div>
            </div>

            <!-- Financial Goal Tracker -->
            <div class="card goal-tracker-section">
                <h2><i class="fas fa-bullseye"></i> Financial Goals</h2>
                <div class="goal-list-container">
                    <?php if (count($goals) > 0): ?>
                        <ul class="goal-list">
                        <?php foreach ($goals as $goal): ?>
                            <li>
                                <div class="goal-info">
                                    <h3><?php echo htmlspecialchars($goal['goal_name']); ?></h3>
                                    <p>Target: ₦<?php echo htmlspecialchars(number_format($goal['target_amount'], 2)); ?></p>
                                    <p>Saved: ₦<?php echo htmlspecialchars(number_format($goal['saved_amount'], 2)); ?></p>
                                </div>
                                <div class="goal-progress">
                                    <?php
                                    $progress = ($goal['saved_amount'] / $goal['target_amount']) * 100;
                                    $progress = min(max($progress, 0), 100); // Clamp between 0 and 100
                                    ?>
                                    <div class="progress-bar-container">
                                        <div class="progress-bar" style="width: <?php echo $progress; ?>%;"></div>
                                    </div>
                                    <span><?php echo round($progress, 1); ?>%</span>
                                </div>
                                <div class="goal-actions">
                                    <button class="action-btn fund-goal-btn" data-goal-id="<?php echo $goal['id']; ?>">Fund</button>
                                    <button class="action-btn withdraw-goal-btn" data-goal-id="<?php echo $goal['id']; ?>">Withdraw</button>
                                    <button class="action-btn delete-goal-btn" data-goal-id="<?php echo $goal['id']; ?>">Delete</button>
                                </div>
                            </li>
                        <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p class="no-data-message">No goals set yet. Start planning for your future!</p>
                    <?php endif; ?>
                </div>
                <button class="btn add-goal-btn"><i class="fas fa-plus-circle"></i> Set a New Goal</button>
            </div>

            <!-- Performance Reporting (Chart) -->
            <div class="card chart-section">
                <h2><i class="fas fa-chart-line"></i> Performance Report</h2>
                <div class="chart-container">
                    <canvas id="performanceChart"></canvas>
                </div>
            </div>
            
            <!-- Interest Calculator -->
            <div class="card calculator-section">
                <h2><i class="fas fa-calculator"></i> Interest Calculator</h2>
                <form id="interestCalculatorForm">
                    <div class="input-group">
                        <label for="principal">Principal Amount (₦)</label>
                        <input type="number" id="principal" name="principal" required>
                    </div>
                    <div class="input-group">
                        <label for="rate">Annual Interest Rate (%)</label>
                        <input type="number" id="rate" name="rate" step="0.01" required>
                    </div>
                    <div class="input-group">
                        <label for="years">Time Period (Years)</label>
                        <input type="number" id="years" name="years" required>
                    </div>
                    <div class="input-group">
                        <label for="frequency">Compounding Frequency</label>
                        <select id="frequency" name="frequency">
                            <option value="1">Annually</option>
                            <option value="4">Quarterly</option>
                            <option value="12">Monthly</option>
                            <option value="365">Daily</option>
                        </select>
                    </div>
                    <button type="submit" class="btn calculate-btn">Calculate Interest</button>
                </form>
                <div id="calculatorResult" class="result-box">
                    <p><strong>Future Value:</strong> <span id="futureValue"></span></p>
                    <p><strong>Total Interest:</strong> <span id="totalInterest"></span></p>
                </div>
            </div>
        </div>
    </main>

    <!-- Modals for Add Asset, Add Goal, Buy, Sell -->
    <div id="addAssetModal" class="modal">
        <div class="modal-content">
            <span class="close-btn">&times;</span>
            <h3>Add New Asset</h3>
            <form id="addAssetForm">
                <div class="input-group">
                    <label for="assetName">Asset Name</label>
                    <select id="assetName" name="asset_name" required>
                        <option value="">Select a stock...</option>
                        <?php foreach ($nigerianStocks as $stock): ?>
                            <option value="<?php echo htmlspecialchars($stock); ?>"><?php echo htmlspecialchars($stock); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="input-group">
                    <label for="quantity">Quantity</label>
                    <input type="number" id="quantity" name="quantity" step="0.0001" placeholder="e.g., 100" required>
                </div>
                <div class="input-group">
                    <label for="purchasePrice">Purchase Price (₦ per unit)</label>
                    <input type="number" id="purchasePrice" name="purchase_price" step="0.01" placeholder="e.g., 250.50" required>
                </div>
                <button type="submit" class="btn btn-primary">Add Asset</button>
            </form>
        </div>
    </div>

    <div id="addGoalModal" class="modal">
        <div class="modal-content">
            <span class="close-btn">&times;</span>
            <h3>Set a New Financial Goal</h3>
            <form id="addGoalForm">
                <div class="input-group">
                    <label for="goalName">Goal Name</label>
                    <input type="text" id="goalName" name="goal_name" placeholder="e.g., New Car Fund" required>
                </div>
                <div class="input-group">
                    <label for="targetAmount">Target Amount (₦)</label>
                    <input type="number" id="targetAmount" name="target_amount" step="0.01" placeholder="e.g., 5,000,000" required>
                </div>
                <div class="input-group">
                    <label for="goalEndDate">Target Date</label>
                    <input type="date" id="goalEndDate" name="end_date" required>
                </div>
                <button type="submit" class="btn btn-primary">Set Goal</button>
            </form>
        </div>
    </div>
    
    <div id="buyAssetModal" class="modal">
        <div class="modal-content">
            <span class="close-btn">&times;</span>
            <h3>Buy New Asset</h3>
            <form id="buyAssetForm">
                <div class="input-group">
                    <label for="buyAssetName">Asset Name</label>
                    <select id="buyAssetName" name="asset_name" required>
                        <option value="">Select a stock...</option>
                        <?php foreach ($nigerianStocks as $stock): ?>
                            <option value="<?php echo htmlspecialchars($stock); ?>"><?php echo htmlspecialchars($stock); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="input-group">
                    <label for="buyQuantity">Quantity</label>
                    <input type="number" id="buyQuantity" name="quantity" step="0.0001" placeholder="e.g., 50" required>
                </div>
                <div class="input-group">
                    <label for="buyPrice">Price (₦ per unit)</label>
                    <input type="number" id="buyPrice" name="price" step="0.01" placeholder="e.g., 400.00" required>
                </div>
                <input type="hidden" name="action" value="buy">
                <button type="submit" class="btn btn-primary">Execute Buy Order</button>
            </form>
        </div>
    </div>

    <div id="sellAssetModal" class="modal">
        <div class="modal-content">
            <span class="close-btn">&times;</span>
            <h3>Sell Asset</h3>
            <form id="sellAssetForm">
                <div class="input-group">
                    <label for="sellAssetName">Asset Name</label>
                    <input type="text" id="sellAssetName" name="asset_name" readonly>
                </div>
                <div class="input-group">
                    <label for="sellQuantity">Quantity to Sell</label>
                    <input type="number" id="sellQuantity" name="quantity" step="0.0001" required>
                </div>
                <div class="input-group">
                    <label for="sellPrice">Price (₦ per unit)</label>
                    <input type="number" id="sellPrice" name="price" step="0.01" placeholder="e.g., 410.00" required>
                </div>
                <input type="hidden" name="action" value="sell">
                <button type="submit" class="btn sell-btn">Execute Sell Order</button>
            </form>
        </div>
    </div>

    <!-- Modals for Funding Goals -->
    <div id="fundGoalModal" class="modal">
        <div class="modal-content">
            <span class="close-btn">&times;</span>
            <h3>Fund a Financial Goal</h3>
            <form id="fundGoalForm">
                <input type="hidden" id="fundGoalId" name="goal_id">
                <div class="input-group">
                    <label for="fundAmount">Amount to Fund (₦)</label>
                    <input type="number" id="fundAmount" name="fund_amount" step="0.01" required>
                </div>
                <button type="submit" class="btn btn-primary">Fund Goal</button>
            </form>
        </div>
    </div>

    <!-- Modals for Withdrawing from Goals -->
    <div id="withdrawGoalModal" class="modal">
        <div class="modal-content">
            <span class="close-btn">&times;</span>
            <h3>Withdraw from Financial Goal</h3>
            <form id="withdrawGoalForm">
                <input type="hidden" id="withdrawGoalId" name="goal_id">
                <div class="input-group">
                    <label for="withdrawAmount">Amount to Withdraw (₦)</label>
                    <input type="number" id="withdrawAmount" name="withdraw_amount" step="0.01" required>
                </div>
                <button type="submit" class="btn btn-primary">Withdraw from Goal</button>
            </form>
        </div>
    </div>


    <script src="scripts.js"></script>
</body>
</html>
