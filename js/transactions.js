function getTransactions() {
  return JSON.parse(localStorage.getItem('klikMaduraTransactions') || '[]');
}

function saveTransactions(transactions) {
  localStorage.setItem('klikMaduraTransactions', JSON.stringify(transactions));
}

function generateOrderNumber() {
  const now = new Date();
  const dateStr = now.getFullYear() + 
                  String(now.getMonth() + 1).padStart(2, '0') + 
                  String(now.getDate()).padStart(2, '0');
  const timeStr = String(now.getHours()).padStart(2, '0') + 
                  String(now.getMinutes()).padStart(2, '0') + 
                  String(now.getSeconds()).padStart(2, '0');
  return `#KM-${dateStr}-${timeStr}`;
}

function addTransaction(transactionData) {
  const transactions = getTransactions();
  const currentUser = getCurrentUser();
  
  const transaction = {
    orderNo: generateOrderNumber(),
    date: new Date().toISOString(),
    status: 'proses',
    userId: currentUser ? currentUser.id : null,
    userName: currentUser ? currentUser.fullName : 'Guest',
    ...transactionData
  };
  
  transactions.unshift(transaction);
  saveTransactions(transactions);
  return transaction;
}

function updateTransactionStatus(orderNo, newStatus) {
  const transactions = getTransactions();
  const transaction = transactions.find(t => t.orderNo === orderNo);
  
  if (transaction) {
    transaction.status = newStatus;
    saveTransactions(transactions);
  }
  
  return transaction;
}

function formatDate(dateStr) {
  const d = new Date(dateStr);
  const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
  return `${d.getDate()} ${months[d.getMonth()]} ${d.getFullYear()}, ${String(d.getHours()).padStart(2,'0')}:${String(d.getMinutes()).padStart(2,'0')}`;
}

function formatMoney(amount) {
  return 'Rp' + amount.toLocaleString('id-ID');
}
