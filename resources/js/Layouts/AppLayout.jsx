import { Link, usePage } from '@inertiajs/react';
import { useState } from 'react';

function HomeIcon({ className }) {
    return (
        <svg className={className} fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor">
            <path strokeLinecap="round" strokeLinejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
        </svg>
    );
}

function ClipboardIcon({ className }) {
    return (
        <svg className={className} fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor">
            <path strokeLinecap="round" strokeLinejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" />
        </svg>
    );
}

function TagIcon({ className }) {
    return (
        <svg className={className} fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor">
            <path strokeLinecap="round" strokeLinejoin="round" d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3Z" />
            <path strokeLinecap="round" strokeLinejoin="round" d="M6 6h.008v.008H6V6Z" />
        </svg>
    );
}

function CubeIcon({ className }) {
    return (
        <svg className={className} fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor">
            <path strokeLinecap="round" strokeLinejoin="round" d="m21 7.5-9-5.25L3 7.5m18 0-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" />
        </svg>
    );
}

function ChartIcon({ className }) {
    return (
        <svg className={className} fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor">
            <path strokeLinecap="round" strokeLinejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" />
        </svg>
    );
}

function LogoutIcon({ className }) {
    return (
        <svg className={className} fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor">
            <path strokeLinecap="round" strokeLinejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
        </svg>
    );
}

function MenuIcon({ className }) {
    return (
        <svg className={className} fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor">
            <path strokeLinecap="round" strokeLinejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5M3.75 17.25h16.5" />
        </svg>
    );
}

function CloseIcon({ className }) {
    return (
        <svg className={className} fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor">
            <path strokeLinecap="round" strokeLinejoin="round" d="M6 18 18 6M6 6l12 12" />
        </svg>
    );
}

const navigation = [
    { name: 'Dashboard', href: '/', icon: HomeIcon },
    { name: 'Orders', href: '/orders', icon: ClipboardIcon },
    { name: 'Categories', href: '/categories', icon: TagIcon },
    { name: 'Items', href: '/items', icon: CubeIcon },
    { name: 'Sales', href: '/sales', icon: ChartIcon },
];

export default function AppLayout({ children, title }) {
    const { auth, flash } = usePage().props;
    const currentPath = window.location.pathname;
    const [sidebarOpen, setSidebarOpen] = useState(false);

    const navLinks = (
        <nav className="mt-4 space-y-1 px-2">
            {navigation.map((item) => {
                const isActive = currentPath === item.href ||
                    (item.href !== '/' && currentPath.startsWith(item.href));
                return (
                    <Link
                        key={item.name}
                        href={item.href}
                        onClick={() => setSidebarOpen(false)}
                        className={`flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium transition-colors ${
                            isActive
                                ? 'bg-gray-100 text-gray-900'
                                : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'
                        }`}
                    >
                        <item.icon className="h-5 w-5" />
                        {item.name}
                    </Link>
                );
            })}
        </nav>
    );

    const userSection = (
        <div className="mt-auto border-t border-gray-200 p-4">
            <div className="flex items-center justify-between">
                <span className="text-sm text-gray-600">{auth?.user?.name}</span>
                <Link
                    href="/logout"
                    method="post"
                    as="button"
                    onClick={() => setSidebarOpen(false)}
                    className="rounded-md p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-600"
                >
                    <LogoutIcon className="h-5 w-5" />
                </Link>
            </div>
        </div>
    );

    return (
        <div className="min-h-screen bg-gray-50 text-gray-900">
            {/* Mobile Sidebar */}
            <div className={`fixed inset-0 z-40 md:hidden ${sidebarOpen ? '' : 'pointer-events-none'}`}>
                <div
                    className={`absolute inset-0 bg-gray-900/40 transition-opacity ${sidebarOpen ? 'opacity-100' : 'opacity-0'}`}
                    onClick={() => setSidebarOpen(false)}
                />
                <div
                    className={`relative flex h-full w-64 flex-col border-r border-gray-200 bg-white transition-transform ${
                        sidebarOpen ? 'translate-x-0' : '-translate-x-full'
                    }`}
                >
                    <div className="flex h-16 items-center justify-between border-b border-gray-200 px-4">
                        <span className="text-lg font-semibold text-gray-900">Raidhaan</span>
                        <button
                            type="button"
                            onClick={() => setSidebarOpen(false)}
                            className="rounded-md p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-600"
                        >
                            <CloseIcon className="h-5 w-5" />
                        </button>
                    </div>
                    {navLinks}
                    {userSection}
                </div>
            </div>

            {/* Desktop Sidebar */}
            <aside className="hidden md:fixed md:inset-y-0 md:flex md:w-64 md:flex-col md:border-r md:border-gray-200 md:bg-white">
                <div className="flex h-16 items-center border-b border-gray-200 px-4">
                    <span className="text-lg font-semibold text-gray-900">Raidhaan</span>
                </div>
                {navLinks}
                {userSection}
            </aside>

            {/* Main Content */}
            <main className="md:pl-64">
                {/* Header */}
                <header className="sticky top-0 z-10 flex h-16 items-center gap-3 border-b border-gray-200 bg-white px-4 md:px-6">
                    <button
                        type="button"
                        onClick={() => setSidebarOpen(true)}
                        className="rounded-md p-2 text-gray-500 hover:bg-gray-100 hover:text-gray-700 md:hidden"
                    >
                        <MenuIcon className="h-5 w-5" />
                    </button>
                    <h1 className="text-lg font-semibold text-gray-900">{title}</h1>
                </header>

                {/* Flash Messages */}
                {flash?.success && (
                    <div className="mx-4 mt-4 rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 md:mx-6">
                        {flash.success}
                    </div>
                )}
                {flash?.error && (
                    <div className="mx-4 mt-4 rounded-md border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700 md:mx-6">
                        {flash.error}
                    </div>
                )}

                {/* Page Content */}
                <div className="p-4 sm:p-6">
                    {children}
                </div>
            </main>
        </div>
    );
}
