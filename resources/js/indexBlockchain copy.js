// resources/js/indexBlockchain.js
import './walletconnect.js' // agar inisialisasi createAppKit dijalankan
import { addContentSmartContract } from './walletconnect.js'

document.addEventListener('DOMContentLoaded', () => {
  const btnAdd = document.getElementById('add-content-btn')
  if (btnAdd) {
    btnAdd.addEventListener('click', async () => {
      try {
        const name = "NamaKonten"
        const createdAt = "2025-01-22"
        const link = "https://example.com/my-content"
        const enrollmentPrice = "100"
        const place = "Location A"
        const contentType = "Video"
        const provider = "Provider X"
        const organizationName = "Org Y"
        const userName = "User ABC"

        const receipt = await addContentSmartContract(
          name,
          createdAt,
          link,
          enrollmentPrice,
          place,
          contentType,
          provider,
          organizationName,
          userName
        )

        alert('Transaksi sukses. Hash: ' + receipt.transactionHash)
      } catch (error) {
        alert('Gagal: ' + error.message)
      }
    })
  }
})
