@extends('layouts.guest')
@section('title', 'Choose Registration Type')

@section('content')
<div class="auth-page">
    <div class="auth-box" style="max-width:560px;">
        <div class="auth-card">
            <div class="auth-header">
                <h2>Create Account</h2>
                <p>Choose the type of account you want to register</p>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-top:8px;">
                {{-- Regular --}}
                <a href="{{ route('register.regular') }}" style="display:flex;flex-direction:column;align-items:center;justify-content:center;gap:14px;padding:32px 20px;border:2px solid #e5e7eb;border-radius:16px;text-decoration:none;color:#1e293b;transition:all 0.2s;background:#fff;" 
                   onmouseover="this.style.borderColor='#2563EB';this.style.boxShadow='0 4px 12px rgba(37,99,235,0.12)'" 
                   onmouseout="this.style.borderColor='#e5e7eb';this.style.boxShadow='none'">
                    <div style="width:56px;height:56px;background:#eff6ff;border-radius:50%;display:flex;align-items:center;justify-content:center;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="#2563EB" stroke-width="1.5" style="width:28px;height:28px;">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                            <circle cx="12" cy="7" r="4"/>
                        </svg>
                    </div>
                    <div style="text-align:center;">
                        <h3 style="font-size:16px;font-weight:700;margin:0;">Regular</h3>
                        <p style="font-size:13px;color:#64748b;margin:4px 0 0;">For regular ferry ticket purchases</p>
                    </div>
                </a>

                {{-- Deportation --}}
                <a href="{{ route('deportation.register') }}" style="display:flex;flex-direction:column;align-items:center;justify-content:center;gap:14px;padding:32px 20px;border:2px solid #e5e7eb;border-radius:16px;text-decoration:none;color:#1e293b;transition:all 0.2s;background:#fff;"
                   onmouseover="this.style.borderColor='#EA580C';this.style.boxShadow='0 4px 12px rgba(234,88,12,0.12)'" 
                   onmouseout="this.style.borderColor='#e5e7eb';this.style.boxShadow='none'">
                    <div style="width:56px;height:56px;background:#fff7ed;border-radius:50%;display:flex;align-items:center;justify-content:center;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="#EA580C" stroke-width="1.5" style="width:28px;height:28px;">
                            <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                            <polyline points="3.27 6.96 12 12.01 20.73 6.96"/>
                            <line x1="12" y1="22.08" x2="12" y2="12"/>
                        </svg>
                    </div>
                    <div style="text-align:center;">
                        <h3 style="font-size:16px;font-weight:700;margin:0;">Deportation</h3>
                        <p style="font-size:13px;color:#64748b;margin:4px 0 0;">For deportation ticket purchases with bus fare</p>
                    </div>
                </a>
            </div>

            <p class="auth-footer-text" style="margin-top:24px;">
                Already have an account? <a href="{{ route('login') }}">Sign In</a>
            </p>
        </div>
    </div>
</div>
@endsection
