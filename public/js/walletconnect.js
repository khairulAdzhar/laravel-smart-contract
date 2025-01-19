// Modified walletconnect.js to support signing
import { createAppKit } from '@reown/appkit/react';
import { EthersAdapter } from '@reown/appkit-adapter-ethers';
import { arbitrum, mainnet, sepolia } from '@reown/appkit/networks';

// Configure AppKit
const projectId = '3555b62326bd83cf91146427f3a9a340';
const networks = [arbitrum, mainnet, sepolia];

const metadata = {
  name: 'My Laravel App',
  description: 'Laravel WalletConnect Integration',
  url: 'https://my-laravel-app.com',
  icons: ['https://my-laravel-app.com/icon.png']
};

const appKit = createAppKit({
  adapters: [new EthersAdapter()],
  networks,
  metadata,
  projectId,
  features: {
    analytics: true
  }
});

let provider, signer, userAccount;

async function initializeWalletConnect() {
  try {
    const wallet = await appKit.connect(); // Connect to WalletConnect
    provider = new ethers.providers.Web3Provider(wallet.provider); // Create ethers provider
    signer = provider.getSigner();
    userAccount = await signer.getAddress(); // Retrieve connected account

    console.log('Wallet connected:', userAccount);
    return { provider, signer, userAccount };
  } catch (error) {
    console.error('WalletConnect initialization failed:', error);
  }
}

export { initializeWalletConnect, provider, signer, userAccount };