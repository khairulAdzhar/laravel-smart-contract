import { createAppKit } from '@reown/appkit/react';
import { EthersAdapter } from '@reown/appkit-adapter-ethers';
import { arbitrum, mainnet, sepolia } from '@reown/appkit/networks';
import { BrowserProvider, ethers } from 'ethers';

// Konfigurasi AppKit
const projectId = '3555b62326bd83cf91146427f3a9a340';
const networks = [arbitrum, mainnet, sepolia];

const metadata = {
  name: 'My Laravel App',
  description: 'Laravel WalletConnect Integration',
  url: 'https://my-laravel-app.com',
  icons: ['https://my-laravel-app.com/icon.png'],
};

// Inisialisasi AppKit
const appKit = createAppKit({
  adapters: [new EthersAdapter()],
  networks,
  metadata,
  projectId,
  features: {
    analytics: true,
  },
});

// Tambahkan variabel global untuk menyimpan informasi
let provider, signer, userAccount = null;

// Inisialisasi Provider dan Signer
(async () => {
  try {
    if (window.ethereum) {
      // Meminta akses ke wallet pengguna
      await window.ethereum.request({ method: 'eth_requestAccounts' });

      // Mendapatkan provider dari browser
      provider = new BrowserProvider(window.ethereum);

      // Mendapatkan signer (pengguna yang terhubung)
      signer = await provider.getSigner();

      // Mendapatkan alamat pengguna
      userAccount = await signer.getAddress();

      // Menyimpan data ke localStorage
      localStorage.setItem('walletProvider', JSON.stringify(provider));
      localStorage.setItem('walletSigner', JSON.stringify(userAccount));
      localStorage.setItem('signer', JSON.stringify(signer));

      console.log('Provider:', provider);
      console.log('Signer Address:', userAccount);
    } else {
      console.error('Ethereum provider not found. Please install MetaMask!');
    }
  } catch (error) {
    console.error('Error initializing WalletConnect:', error);
  }
})();

