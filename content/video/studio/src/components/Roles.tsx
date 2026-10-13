import React from 'react';
import {C, FONT_AR, FONT_EN} from '../brand';
import {Icon, IconName} from './Icons';
import {useSpring} from './Kit';

export type Role = {key: string; ar: string; en: string; icon: IconName; panel: string; does: string; doesEn: string; shot: string};

// What each role does — from docs/product/02-USERS-AND-ROLES.md.
export const ROLES: Role[] = [
  {key: 'admin', ar: 'الإدارة', en: 'Admin', icon: 'shield', panel: '/admin', shot: 'd-admin', does: 'المستخدمين والأدوار والصلاحيات والإعدادات', doesEn: 'Users, roles, permissions & settings'},
  {key: 'staff', ar: 'الموظفين', en: 'Staff', icon: 'briefcase', panel: '/staff', shot: 'd-staff', does: 'مراجعة العقارات والطلبات والعملاء والصيانة', doesEn: 'Moderation, leads, bookings & maintenance'},
  {key: 'agent', ar: 'الوكلاء', en: 'Agents', icon: 'handshake', panel: '/agent', shot: 'd-agent', does: 'العملاء المحتملون والمعاينات وعقاراتي', doesEn: 'Leads, viewings & my listings'},
  {key: 'landlord', ar: 'الملّاك', en: 'Landlords', icon: 'key', panel: '/landlord', shot: 'd-landlord', does: 'العقود والإيجارات والمدفوعات', doesEn: 'Leases, rent & payments'},
  {key: 'tenant', ar: 'المستأجرين', en: 'Tenants', icon: 'home', panel: '/tenant', shot: 'd-tenant', does: 'عقدي ودفعاتي وطلبات الصيانة', doesEn: 'My lease, payments & repair requests'},
  {key: 'buyer', ar: 'المشترين', en: 'Buyers', icon: 'cart', panel: '/buyer', shot: 'd-buyer', does: 'المفضلة وحجوزات المعاينة', doesEn: 'Wishlist & viewing bookings'},
  {key: 'seller', ar: 'البائعين', en: 'Sellers', icon: 'tag', panel: '/seller', shot: 'd-seller', does: 'عقاراتي المعروضة والحجوزات عليها', doesEn: 'My listings & their bookings'},
  {key: 'contractor', ar: 'المقاولين', en: 'Contractors', icon: 'hardhat', panel: '/contractor', shot: 'd-contractor', does: 'طلبات الصيانة المسندة وتحديث حالتها', doesEn: 'Assigned repair jobs & status updates'},
];

export const RoleCard: React.FC<{role: Role; delay: number; k: number; en?: boolean; w?: number}> = ({role, delay, k, en = false, w = 220}) => {
  const s = useSpring(delay, 12);
  return (
    <div
      style={{
        width: w * k,
        padding: `${26 * k}px ${10 * k}px`,
        borderRadius: 30 * k,
        background: 'rgba(255,255,255,.07)',
        border: '1.5px solid rgba(255,255,255,.14)',
        display: 'flex',
        flexDirection: 'column',
        alignItems: 'center',
        gap: 14 * k,
        opacity: s,
        transform: `translateY(${(1 - s) * 40}px) scale(${0.8 + 0.2 * s})`,
      }}
    >
      <div
        style={{
          width: 88 * k,
          height: 88 * k,
          borderRadius: 24 * k,
          background: 'linear-gradient(135deg,#1F6FF2,#19C3B1)',
          display: 'flex',
          alignItems: 'center',
          justifyContent: 'center',
        }}
      >
        <Icon name={role.icon} size={48 * k} color="#fff" />
      </div>
      <div style={{fontFamily: en ? FONT_EN : FONT_AR, fontWeight: 800, fontSize: 34 * k, color: C.white}}>{en ? role.en : role.ar}</div>
    </div>
  );
};
