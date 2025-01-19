/**
 * File: resources/js/walletconnect.js
 */
import { createAppKit } from '@reown/appkit/react'
import { EthersAdapter } from '@reown/appkit-adapter-ethers'
// Diimpor dari @reown/appkit/networks, pastikan versi library 
// sudah mendukung sepolia (chainId 11155111)
import { arbitrum, mainnet, sepolia } from '@reown/appkit/networks'
import { BrowserProvider } from 'ethers'

// ---- 1) KONFIGURASI DASAR ---------------------------------- //
const projectId = '3555b62326bd83cf91146427f3a9a340' 
/**
 * networks array, pastikan sepolia sudah ada di sini:
 * chain ID = 11155111
 */
const networks = [arbitrum, mainnet, sepolia]

const metadata = {
  name: 'My Laravel App',
  description: 'Laravel WalletConnect Integration',
  url: 'https://my-laravel-app.com',
  icons: ['https://my-laravel-app.com/icon.png']
}

// ---- 2) INISIALISASI APPKIT -------------------------------- //
/**
 * createAppKit() mengembalikan instance appKit,
 * namun TIDAK kita pakai event appKitInstance.on()
 * sebab versi library Anda tidak menyediakan method .on().
 */
const appKitInstance = createAppKit({
  adapters: [new EthersAdapter()],
  networks,
  metadata,
  projectId,
  features: {
    analytics: true
  }
})

// ---- 3) FUNGSI GLOBAL UNTUK MENYIAPKAN SIGNER -------------- //
/**
 * initWalletConnectSigner():
 * - Periksa apakah adapter EVM (eip155) sudah "connected"
 * - Jika ya, buat window.signer & window.userAddress
 * - Jika belum, kosongkan
 */
window.initWalletConnectSigner = async function () {
  try {
    const eip155Adapter = appKitInstance.getAdapter('eip155')
    if (!eip155Adapter) {
      console.warn('[WalletConnect] No eip155 adapter found in appKitInstance.')
      window.signer = null
      window.userAddress = null
      return
    }

    // Debug: periksa chainId 
    // (Jika user benar-benar di Sepolia, seharusnya chainId = 11155111)
    console.log('[DEBUG] eip155Adapter:', eip155Adapter)

    // isConnected akan true hanya jika chain user 
    // masuk di daftar networks & user menekan "Connect" 
    // dan menuntaskan proses connect 
    if (eip155Adapter.isConnected) {
      console.log('[DEBUG] eip155Adapter chainId:', eip155Adapter.chainId)
      // 11155111 = Sepolia, 1 = Mainnet, 42161 = Arbitrum, dsb

      // Jika chain-nya benar, bungkus provider ke ethers
      const ethersProvider = new BrowserProvider(eip155Adapter.provider)
      const signer = await ethersProvider.getSigner()

      // Simpan global
      window.signer = signer
      window.userAddress = eip155Adapter.address
      console.log('[WalletConnect] signer is ready:', window.userAddress)
    } else {
      console.warn('[WalletConnect] not connected or chain not in networks.')
      window.signer = null
      window.userAddress = null
    }
  } catch (err) {
    console.error('[WalletConnect] initSigner failed:', err)
    window.signer = null
    window.userAddress = null
  }
}
