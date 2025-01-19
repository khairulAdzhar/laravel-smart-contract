// resources/js/deploy-contract.js

import { ethers } from 'ethers'
import { getAppKit } from '@reown/appkit'

// Pastikan jQuery tersedia (seringkali sudah global di window.$, 
// tapi jika Anda mengimpor jQuery via npm, import di sini).
// import $ from 'jquery'

$(document).ready(function () {
    // Inisialisasi DataTable contoh (sesuaikan dengan rute & kolom Anda)
    const table = $('.data-table').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        pageLength: 50,
        ajax: "/show-content-blockchain",  // contoh route
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', searchable: false },
            { data: 'name', name: 'name' },
            { data: 'network', name: 'network' },
            { data: 'tx_hash', name: 'tx_hash' },
            { data: 'blockchain_id', name: 'blockchain_id' },
            { data: 'logs', name: 'logs' },
            { data: 'block_id', name: 'block_id' },
            { data: 'status', name: 'status' },
            { data: 'action', name: 'action' }
        ],
        // dsb...
    })

    let signer = null
    let userAccount = null

    async function updateWalletConnection() {
        try {
            const appKit = getAppKit()
            if (!appKit) {
                console.warn('AppKit not found! Pastikan walletconnect.js sudah diload sebelum script ini.')
                return
            }

            const ethersAdapter = appKit.adapters.find(adp => adp.name === 'EthersAdapter')
            if (!ethersAdapter || !ethersAdapter.provider) {
                console.warn('EthersAdapter provider not found, kemungkinan user belum connect.')
                return
            }

            // Membuat Web3Provider dari provider WalletConnect
            const web3Provider = new ethers.providers.Web3Provider(ethersAdapter.provider)
            signer = web3Provider.getSigner()

            userAccount = await signer.getAddress()
            console.log('Connected with account:', userAccount)
        } catch (err) {
            console.error('updateWalletConnection error:', err)
        }
    }

    // Panggil saat awal load
    updateWalletConnection()

    // Contoh handle klik Sign
    $(document).on('click', '.add-content-btn', async function() {
        if (!signer) {
            Swal.fire({
                title: 'Wallet Belum Terkoneksi!',
                text: 'Mohon klik "Connect" terlebih dahulu.',
                icon: 'error',
                confirmButtonText: 'OK'
            })
            return
        }

        // Di sini ambil data dari attribute
        const contentId = $(this).data('content-id') || 'none'
        // dsb...

        try {
            // Contoh panggil contract
            const CONTRACT_ADDRESS = "0x1234abcd..." // ganti dgn address
            const contractABI = [ /* ... ABI ... */ ]
            const contract = new ethers.Contract(CONTRACT_ADDRESS, contractABI, signer)

            // Kirim transaksi
            const tx = await contract.addContent(/* ... argumen ... */)
            const receipt = await tx.wait()

            console.log('TxHash:', receipt.transactionHash)

            // Kirim data ke server via fetch
            const response = await fetch('/save-deployed-data', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                body: JSON.stringify({
                    content_id: contentId,
                    user_wallet_address: userAccount,
                    tx_hash: receipt.transactionHash,
                    block_no: receipt.blockNumber,
                    // dsb...
                })
            })

            if (response.ok) {
                Swal.fire({
                    title: 'Berhasil!',
                    text: 'Content sudah disimpan di Blockchain',
                    icon: 'success',
                    confirmButtonText: 'OK'
                })
                table.ajax.reload()
            } else {
                Swal.fire({
                    title: 'Gagal Menyimpan!',
                    text: 'Server error saat menyimpan data!',
                    icon: 'error',
                    confirmButtonText: 'OK'
                })
            }

        } catch (error) {
            console.error(error)
            Swal.fire({
                title: 'Transaksi Gagal!',
                text: error?.message || 'Unknown error.',
                icon: 'error',
                confirmButtonText: 'OK'
            })
        }
    })
})
