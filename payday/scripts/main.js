function calc() {
  const amt = parseFloat(document.getElementById('loanAmount').value);
  const result = document.getElementById('result');
  if (!amt || amt <= 0) {
    result.textContent = "Please enter a valid positive amount.";
    result.classList.add('text-danger');
    return;
  }
  const repay = (amt * 1.2).toFixed(2);
  result.textContent = `Repay SBD ${repay} in 14 days`;
  result.classList.remove('text-danger');
}
